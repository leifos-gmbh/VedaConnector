<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Adapter;

use Exception;
use ilCourseMembershipMailNotification;
use ilCourseParticipants;
use ilFavouritesManager;
use ilLogLevel;
use ilObjCourse;
use ilObject;
use ilObjectFactory;
use ilObjUser;
use ilParticipants;
use ilRbacAdmin;
use InvalidArgumentException;
use Leifos\VedaConnector\I\Api\Adapter\MemberStandardImportInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseCompanionsInterface as CourseCompanionsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseMembersInterface as CourseMembersInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseTutorsInterface as CourseTutorsInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\HandlerInterface as ApiElearningPlattformInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status as CourseStatusStatus;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type as CourseStatusType;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface as CourseStatusDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type as MailType;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;
use Leifos\VedaConnector\I\UDF\DB\HandlerInterface as UDFDBInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface as UtilsInterface;

class MemberStandardImport implements MemberStandardImportInterface
{
    protected const REGULAR = 'REGULAER';
    protected const TEMPORARY = 'TEMPORAER';

    /**
     * @var int[]
     */
    protected array $new_assignments;

    public function __construct(
        protected LoggerInterface $logger,
        protected ilRbacAdmin $rbac_admin,
        protected UDFDBInterface $udf_db,
        protected CourseStatusDBInterface $course_status_db,
        protected BuilderFactoryInterface $builder_factory,
        protected SettingsInterface $settings,
        protected ApiElearningPlattformInterface $elearning_api,
        protected UtilsInterface $utils,
    ) {
        $this->new_assignments = [];
    }

    public function import() : void
    {
        $this->logger->debug('Reading "ELearning-Kurse" ...');
        $standard_courses = $this->course_status_db->lookupByStatusAndType(
            CourseStatusStatus::SYNCHRONIZED,
            CourseStatusType::STANDARD
        );
        foreach ($standard_courses as $standard_cours) {
            $oid = $standard_cours->getOid();
            $obj_id = $standard_cours->getObjId();
            if (!$this->ensureCourseExists($obj_id)) {
                $this->logger->warning('Ignoring deleted course with id: ' . $obj_id);
                continue;
            }
            $this->synchronizeParticipants($oid, $obj_id);
        }
    }

    protected function synchronizeParticipants(string $oid, int $obj_id) : void
    {
        $tutors = $this->elearning_api->requestCourseTutors($oid);
        $companions = $this->elearning_api->requestCourseCompanions($oid);
        $members = $this->elearning_api->requestCourseMembers($oid);
        if (
            is_null($tutors) ||
            is_null($companions) ||
            is_null($members)
        ) {
            $this->logger->warning('Api connection failed');
            return;
        }
        $participants = $this->initParticipants($obj_id);
        $course = $this->initCourse($obj_id);
        $this->removeDeprecatedMembers($participants, $course, $members);
        $this->addNewMembers($participants, $course, $members);
        $this->handleRemoveDeprecatedTutorsAndCompanions($course, $participants, $tutors, $companions);
        $this->handleTutorAssignments($course, $participants, $tutors);
        $this->handleCompanionAssignments($course, $participants, $companions);
    }

    protected function removeDeprecatedMembers(
        ilCourseParticipants $part,
        ilObjCourse $crs,
        CourseMembersInterface $members
    ) : void {
        $this->logger->debug('Removing deprecated members');
        foreach ($part->getMembers() as $usr_id) {
            $usr_oid = ilObject::_lookupImportId($usr_id);
            if (!$usr_oid) {
                $this->logger->debug('Keep member assignment for non synchonised account.');
            }
            if (!$members->containsMemberWithOID($usr_oid)) {
                $message = 'Deassigning user: ' . $usr_id . ' with oid ' . $usr_oid . ' from course: ' . $crs->getTitle();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser(
                    $crs->getDefaultMemberRole(),
                    $usr_id
                );
                $this->builder_factory->mailSegment()
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->withMessage($message)
                    ->store();
            }
        }
    }

    protected function addNewMembers(
        ilCourseParticipants $participants,
        ilObjCourse $course,
        CourseMembersInterface $members
    ) : void {
        $this->logger->debug('Adding new members');
        foreach ($members as $member) {
            $this->logger->debug('Validating ' . $member->getTeilnehmerId());
            $user_id = $this->getUserIdForImportId($member->getTeilnehmerId());
            $this->logger->debug('Found usr_id: ' . $user_id);
            if (!$user_id) {
                $this->logger->warning('Cannot find user id for import_id: ' . $member->getTeilnehmerId());
                continue;
            }
            if ($participants->isMember($user_id)) {
                $this->logger->debug('User with id: ' . $user_id . ' is already assigned to course: ' . $course->getTitle());
                continue;
            }
            if ($this->utils->isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())) {
                $this->assignUserToRole(
                    $course->getDefaultMemberRole(),
                    $user_id,
                    $participants,
                    $course
                );
            } else {
                $this->logger->info('Ignoring user with access: ' . $member->getKursZugriffAb()->format('Y-m-d') . ', ' . $member->getKursZugriffBis()->format('Y-m-d'));
            }
        }
    }

    protected function initCourse(
        int $obj_id
    ) : ilObjCourse {
        $refs = ilObject::_getAllReferences($obj_id);
        $ref_id = end($refs);
        $course = ilObjectFactory::getInstanceByRefId($ref_id, false);
        if (!$course instanceof ilObjCourse) {
            $message = 'Invalid course id given: ' . $obj_id;
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
            throw new InvalidArgumentException($message);
        }
        return $course;
    }

    protected function initParticipants(
        int $obj_id
    ) : ilCourseParticipants {
        $refs = ilObject::_getAllReferences($obj_id);
        $ref_id = end($refs);
        $participants = ilParticipants::getInstance($ref_id);
        if (!$participants instanceof ilCourseParticipants) {
            $message = 'Invalid participant id given: ' . $obj_id;
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
            throw new InvalidArgumentException($message);
        }
        return $participants;
    }

    protected function ensureCourseExists(int $obj_id) : bool
    {
        $refs = ilObject::_getAllReferences($obj_id);
        $ref_id = end($refs);
        try {
            $course = ilObjectFactory::getInstanceByRefId($ref_id, false);
            if ($course instanceof ilObjCourse) {
                return true;
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

    protected function assignUserToRole(
        int $role,
        int $user,
        ilCourseParticipants $part,
        ilObjCourse $course
    ) : void {
        $this->rbac_admin->assignUser($role, $user);
        if (!in_array($user, $this->new_assignments)) {
            $this->logger->debug('Adding new user sending mail notification...');
            $part->sendNotification(ilCourseMembershipMailNotification::TYPE_ADMISSION_MEMBER, $user);
            $favourites = new ilFavouritesManager();
            $favourites->add(
                $user,
                $course->getRefId()
            );
            $this->new_assignments[] = $user;
            $this->builder_factory->mailSegment()
                ->withType(MailType::MEMBERSHIP_UPDATED)
                ->withMessage('Adding new user with role_id ' . $role . ' user_id ' . $user . ' to course with ref_id ' . $course->getRefId())
                ->store();
        }
    }

    protected function getUserIdForImportId(?string $oid) : ?int
    {
        return is_null($oid) ? null : ilObject::_lookupObjIdByImportId($oid);
    }

    protected function handleRemoveDeprecatedTutorsAndCompanions(
        ilObjCourse $course,
        ilCourseParticipants $participants,
        CourseTutorsInterface $remote_tutors,
        CourseCompanionsInterface $remote_companions
    ) : void
    {
        $valid_tutor_import_ids_with_udf_field_entry = [];
        $this->logger->info("Removing tutors with udf field entries that are not within a valid date range.");
        foreach ($participants->getTutors() as $tutor_id) {
            $tutor = ilObjectFactory::getInstanceByObjId($tutor_id, false);
            if (!$tutor instanceof ilObjUser) {
                $this->logger->warning('Found invalid tutor: ' . $tutor_id);
                continue;
            }
            $udf_data = $tutor->getUserDefinedData();
            $tutor_oid = $udf_data['f_' . $this->settings->read(SettingsName::UDF_TUTOR_ID)] ?? '';
            $companion_oid = $udf_data['f_' . $this->settings->read(SettingsName::UDF_COMPANION_ID)] ?? '';
            $supervisor_oid = $udf_data['f_' . $this->settings->read(SettingsName::UDF_SUPERVISOR_ID)] ?? '';

            if (!$tutor_oid && !$companion_oid && !$supervisor_oid) {
                $this->logger->warning('Ignoring tutor without tutor_oid: ' . $tutor->getLogin());
                continue;
            }

            $found = false;
            foreach ($remote_tutors as $remote_tutor) {
                if (!$this->utils->isValidDate($remote_tutor->getKursZugriffAb(), $remote_tutor->getKursZugriffBis())) {
                    $this->logger->debug(
                        'Ignoring tutor outside time frame: ' .
                        $remote_tutor->getDozentId()
                    );
                    continue;
                }
                if ($this->utils->compareOidsEqual($remote_tutor->getDozentId(), $tutor_oid)) {
                    $found = true;
                    break;
                }
            }
            foreach ($remote_companions as $remote_companion) {
                if (!$this->utils->isValidDate($remote_companion->getKursZugriffAb(), $remote_companion->getKursZugriffBis())) {
                    $this->logger->debug('Ignoring companion outside time frame: ' . $remote_companion->getLernbegleiterId());
                    continue;
                }
                // fix mixed companion and supervisor
                if ($this->utils->compareOidsEqual($remote_companion->getLernbegleiterId(), $companion_oid)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $message = 'Deassigning deprecated tutor from course: ' . $tutor->getLogin();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser($course->getDefaultTutorRole(), $tutor_id);
                $participants->updateContact($tutor_id, false);
                $this->builder_factory->mailSegment()
                    ->withMessage($message)
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->store();
            }
            if ($found) {
                $valid_tutor_import_ids_with_udf_field_entry[] = $tutor->getImportId();
            }
        }

        $this->logger->debug('Removing tutors without remote');
        foreach ($participants->getTutors() as $user_id) {
            $import_id = ilObject::_lookupImportId($user_id);
            if (!$import_id) {
                $this->logger->debug('Keep member assignment for non synchonised account.');
                continue;
            }
            if (in_array($import_id, $valid_tutor_import_ids_with_udf_field_entry)) {
                $this->logger->debug('Keep member assignment if udf field entry exists, independend of if a remote entry exists.');
                continue;
            }
            $this->logger->debug("Number of tutors: " . $remote_tutors->count());
            if (
                !$remote_tutors->containsTutorWithOID($import_id) &&
                !$remote_companions->containsCompanionWithOID($import_id)
            ) {
                $message = 'Deassigning tutor: ' . $user_id . ' with oid ' . $import_id . ' from course: ' . $course->getTitle();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser(
                    $course->getDefaultTutorRole(),
                    $user_id
                );
                $this->builder_factory->mailSegment()
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->withMessage($message)
                    ->store();
            }
        }
    }

    protected function handleTutorAssignments(
        ilObjCourse $course,
        ilCourseParticipants $participants,
        CourseTutorsInterface $remote_tutors
    ) : void {
        $this->logger->info("Assigning tutors to course " . $course->getTitle() . " by elearningbenutzeraccountid");
        foreach ($remote_tutors as $tutor) {
            $user_id = $this->getUserIdForImportId($tutor->getElearningbenutzeraccountId());
            if (!$user_id) {
                $this->logger->warning('Cannot find user id for import_id: ' . $tutor->getElearningbenutzeraccountId());
                continue;
            }
            if ($participants->isMember($user_id)) {
                $this->logger->debug('User with id: ' . $user_id . ' is already assigned to course: ' . $course->getTitle());
                continue;
            }
            if ($this->utils->isValidDate($tutor->getKursZugriffAb(), $tutor->getKursZugriffBis())) {
                $this->assignUserToRole(
                    $course->getDefaultTutorRole(),
                    $user_id,
                    $participants,
                    $course
                );
            }
        }
        $this->logger->info("Assigning tutors to course  " . $course->getTitle() . " by dozent id in udf field.");
        foreach ($remote_tutors as $remote_tutor) {
            $tutor_oid = $remote_tutor->getDozentId();
            $this->logger->debug('Remote tutor oid is: ' . $tutor_oid);
            $this->logger->dump($this->udf_db->getUsersForTutorId($tutor_oid), ilLogLevel::DEBUG);
            if (!$this->utils->isValidDate($remote_tutor->getKursZugriffAb(), $remote_tutor->getKursZugriffBis())) {
                $this->logger->info('Outside time frame: Ignoring tutor with id: ' . $tutor_oid);
                continue;
            }
            foreach ($this->udf_db->getUsersForTutorId($tutor_oid) as $uid) {
                $this->logger->info("Uid: " . $uid);
                if (!in_array($uid, $participants->getTutors())) {
                    $message = 'Assigning new course tutor with id: ' . $tutor_oid . ' ILIAS id: ' . $uid;
                    $this->logger->info($message);
                    $this->rbac_admin->assignUser($course->getDefaultTutorRole(), $uid);
                    $participants->updateContact($uid, true);
                    $this->builder_factory->mailSegment()
                        ->withMessage('Remote tutor oid is: ' . $tutor_oid)
                        ->withType(MailType::MEMBERSHIP_UPDATED)
                        ->store();
                }
            }
        }
    }

    protected function handleCompanionAssignments(
        ilObjCourse $course,
        ilCourseParticipants $participants,
        CourseCompanionsInterface $remote_companions
    ) : void {
        $this->logger->info("Assigning companions to course " . $course->getTitle() . "by elearningbenutzeraccountid.");
        foreach ($remote_companions as $companion) {
            $user_id = $this->getUserIdForImportId($companion->getElearningbenutzeraccountId());
            if (!$user_id) {
                $this->logger->warning('Cannot find user id for import_id: ' . $companion->getElearningbenutzeraccountId());
                continue;
            }
            if ($participants->isMember($user_id)) {
                $this->logger->debug('User with id: ' . $user_id . ' is already assigned to course: ' . $course->getTitle());
                continue;
            }
            if ($this->utils->isValidDate($companion->getKursZugriffAb(), $companion->getKursZugriffBis())) {
                $this->assignUserToRole(
                    $course->getDefaultTutorRole(),
                    $user_id,
                    $participants,
                    $course
                );
            }
        }
        $this->logger->info("Assigning companions to course " . $course->getTitle() . " by companion id in udf field.");
        foreach ($remote_companions as $remote_companion) {
            $companion_oid = $remote_companion->getLernbegleiterId();
            $this->logger->debug('Remote companion oid is: ' . $companion_oid);
            $this->logger->dump($this->udf_db->getUsersForCompanionId($companion_oid), ilLogLevel::DEBUG);
            if (!$this->utils->isValidDate($remote_companion->getKursZugriffAb(), $remote_companion->getKursZugriffBis())) {
                $this->logger->info('Outside time frame: Ignoring companion with id: ' . $companion_oid);
                continue;
            }
            // fix mixed companion / supervisor id
            foreach ($this->udf_db->getUsersForCompanionId($companion_oid) as $uid) {
                if (!in_array($uid, $participants->getTutors())) {
                    $message = 'Assigning new course companion with id: ' . $companion_oid . ' ILIAS id: ' . $uid;
                    $this->logger->info($message);
                    $this->rbac_admin->assignUser($course->getDefaultTutorRole(), $uid);
                    $participants->updateContact($uid, true);
                    $this->builder_factory->mailSegment()
                        ->withMessage($message)
                        ->withType(MailType::MEMBERSHIP_UPDATED)
                        ->store();
                }
            }
        }
    }
}
