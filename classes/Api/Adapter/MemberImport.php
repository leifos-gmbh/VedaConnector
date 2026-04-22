<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Adapter;

use DateTime;
use ilCourseMembershipMailNotification;
use ilCourseParticipants;
use ilExAssignment;
use ilExSubmission;
use ilFavouritesManager;
use ilLogLevel;
use ilLPStatus;
use ilObjCourse;
use ilObject;
use ilObjectFactory;
use ilObjExercise;
use ilObjUser;
use ilParticipants;
use ilRbacAdmin;
use ilRbacReview;
use ilVedaMemberImportException;
use Leifos\VedaConnector\I\Api\Adapter\MemberImportInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\MembersInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\HandlerInterface as ApiTrainingProgramInterface;
use Leifos\VedaConnector\I\Api\TrainingProgramModules\HandlerInterface as ApiTrainingProgramModulesInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseStatusInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type as MailType;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status as UserStatus;
use Leifos\VedaConnector\Utils;

class MemberImport implements MemberImportInterface
{
    protected const REGULAR = 'REGULAER';

    protected const TEMPORARY = 'TEMPORAER';

    public function __construct(
        protected LoggerInterface $logger,
        protected ilRbacAdmin $rbac_admin,
        protected ilRbacReview $rbac_review,
        protected ilVedaUDFClaimingPlugin $udf_claiming_plugin,
        protected ilVedaMDClaimingPluginDBManagerInterface $md_db_manager,
        protected BuilderFactoryInterface $builder_factory,
        protected ApiTrainingProgramModulesInterface $training_program_modules_api,
        protected ApiTrainingProgramInterface $training_program_api
    ) {
    }

    public function import() : void
    {
        $this->logger->debug('Reading "Ausbildungszüge" ...');
        foreach ($this->md_db_manager->findTrainingCourseTrains() as $oid) {
            $this->importTrainingCourseTrain($oid);
        }
    }

    public function handleTrackingEvent(
        int $obj_id,
        int $usr_id,
        int $status
    ) : void {
        if ($status != ilLPStatus::LP_STATUS_COMPLETED_NUM) {
            $this->logger->debug('Ignoring non completed event.');
            return;
        }
        $usr_oid = ilObjUser::_lookupImportId($usr_id);
        if (!$usr_oid) {
            $this->logger->debug('Not imported user.');
            return;
        }
        // additional check in user status table
        $us = $this->builder_factory->userStatus()->withOID($usr_oid)->get();
        if ($us->getCreationStatus() != UserStatus::SYNCHRONIZED) {
            $this->logger->info('Ignoring not synchronized user account: ' . $usr_oid);
            return;
        }
        if (ilObject::_lookupType($obj_id) != 'exc') {
            $this->logger->debug('Ignoring non session event');
            return;
        }

        $refs = ilObject::_getAllReferences($obj_id);
        $ref = end($refs);

        $segment_id = $this->md_db_manager->findTrainSegmentId($ref);

        if (!$segment_id) {
            $this->logger->debug('Not ausbildungszugabschnitt');
            return;
        }
        $this->sendExerciseSuccessInformation($obj_id, $usr_id, $usr_oid, $segment_id);
    }

    protected function sendExerciseSuccessInformation(
        int $obj_id,
        int $usr_id,
        string $usr_oid,
        string $segment_id
    ) : void {
        // find parent courses
        $exercise = ilObjectFactory::getInstanceByObjId($obj_id, false);
        if (!$exercise instanceof ilObjExercise) {
            $this->logger->warning('Cannot create exercise instance');
            return;
        }

        // find ref_ids for exercise
        $refs = ilObject::_getAllReferences($exercise->getId());

        $is_practical_training = false;
        $is_self_learning = false;
        $submission_date_str = '';
        foreach ($refs as $tmp => $ref_id) {
            $segment_id = $this->md_db_manager->findTrainSegmentId($ref_id);
            $this->logger->debug('Current ref_id: ' . $ref_id . ' has segment_id: ' . $segment_id);
            $segment_info = $this->builder_factory->trainingProgrammModule()
                ->withOID($segment_id)
                ->get();
            if ($segment_info->isPracticalTraining()) {
                $this->logger->info('Exercise of type "practical training"');
                $is_practical_training = true;
            } elseif ($segment_info->isSelfLearning()) {
                $this->logger->info('Exercise of type "self learning"');
                $is_self_learning = true;
            } else {
                $this->logger->info('No practical training type, no self learning type');
                break;
            }
            $assignments = ilExAssignment::getInstancesByExercise($exercise->getId());
            foreach ($assignments as $assignment) {
                $submission = new ilExSubmission($assignment, $usr_id);
                $submission_date_str = $submission->getLastSubmission();
                $this->logger->notice('Last submission is: ' . $submission_date_str);
            }
            break;
        }

        if ($is_practical_training && $submission_date_str) {
            $submission_date = new DateTime($submission_date_str);
            if (
                !$this->training_program_modules_api->sendExerciseSubmissionDate($segment_id, $usr_oid, $submission_date) ||
                !$this->training_program_modules_api->sendExerciseSubmissionConfirmed($segment_id, $usr_oid, new DateTime()) ||
                !$this->training_program_modules_api->sendExerciseSuccess($segment_id, $usr_oid, new DateTime())
            ) {
                $this->logger->error('Send exercise success failed');
            }
        } elseif ($is_practical_training) {
            $ref_id = (count($refs) > 0) ? ('' . $refs[count($refs) - 1]) : 'NOT FOUND';
            $this->logger->notice('Did not send exercise success messages for user without submission. ');
            $this->logger->notice('User id: ' . $usr_id);
            $this->logger->notice('Exercise ref_id: ' . $ref_id);
        }
        if ($is_self_learning) {
            if (!$this->training_program_modules_api->sendExerciseSuccess($segment_id, $usr_oid, new DateTime())) {
                $this->logger->error('Send exercise success for type "self training" failed');
            }
        }
    }

    protected function importTrainingCourseTrain(
        ?string $oid
    ) : void {
        // read member info
        $members = $this->training_program_api->requestMembers($oid);
        $course_ref_id = $this->md_db_manager->findTrainingCourseTrain($oid);
        $course = ilObjectFactory::getInstanceByRefId($course_ref_id);
        if (!$course instanceof ilObjCourse) {
            $message = 'Cannot find course for oid: ' . $oid;
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
            throw new ilVedaMemberImportException($message);
        }
        $participants = ilParticipants::getInstance($course_ref_id);
        if (!$participants instanceof ilCourseParticipants) {
            $message = 'Cannot find course participants for oid: ' . $oid;
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
            throw new ilVedaMemberImportException($message);
        }

        $this->logger->debug('Handling course: ' . $course->getTitle());

        $veda_crs = $this->course_db_factory->elementBuilder()
            ->withOID($oid)
            ->get();

        $currently_assigned = $participants->getParticipants();

        if (is_null($members)) {
            return;
        }

        $this->removeInvalidRegularMembers($course, $members);
        $this->removeInvalidPermanentSwitchMembers($course, $members, $veda_crs);
        $this->removeInvalidTemporarySwitchMembers($course, $members, $veda_crs);
        $this->addRegularMembers($course, $participants, $members, $currently_assigned);
        $this->addPermanentSwitchMembers($course, $participants, $members, $veda_crs, $currently_assigned);
        $this->addTemporarySwitchMembers($course, $participants, $members, $veda_crs, $currently_assigned);
        $this->handleTutorAssignments($course, $participants, $oid);
    }

    protected function handleTutorAssignments(
        ilObjCourse $course,
        ilCourseParticipants $participants,
        ?string $oid
    ) : bool {
        $udffields = $this->udf_claiming_plugin->getFields();
        $remote_tutors = $this->training_program_api->requestTutors($oid);
        $remote_companions = $this->training_program_api->requestCompanions($oid);
        $remote_supervisors = $this->training_program_api->requestSupervisors($oid);

        if (
            is_null($remote_tutors) ||
            is_null($remote_companions) ||
            is_null($remote_supervisors)
        ) {
            $this->logger->warning('Reading assigned tutors failed. Aborting tutor update');
            return false;
        }

        // deassign deprecated tutors
        foreach ($participants->getTutors() as $tutor_id) {
            $tutor = ilObjectFactory::getInstanceByObjId($tutor_id, false);
            if (!$tutor instanceof ilObjUser) {
                $this->logger->warning('Found invalid tutor: ' . $tutor_id);
                continue;
            }
            $udf_data = $tutor->getUserDefinedData();
            $tutor_oid = '';
            if (isset($udf_data['f_' . $udffields[VedaUDFClaimingFields::TUTOR_ID->value]])) {
                $tutor_oid = $udf_data['f_' . $udffields[VedaUDFClaimingFields::TUTOR_ID->value]];
            }
            $companion_oid = '';
            if (isset($udf_data['f_' . $udffields[VedaUDFClaimingFields::COMPANION_ID->value]])) {
                $companion_oid = $udf_data['f_' . $udffields[VedaUDFClaimingFields::COMPANION_ID->value]];
            }
            $supervisor_oid = '';
            if (isset($udf_data['f_' . $udffields[VedaUDFClaimingFields::SUPERVISOR_ID->value]])) {
                $supervisor_oid = $udf_data['f_' . $udffields[VedaUDFClaimingFields::SUPERVISOR_ID->value]];
            }
            if (!$tutor_oid && !$companion_oid && !$supervisor_oid) {
                $this->logger->debug('Ignoring tutor without tutor_oid: ' . $tutor->getLogin());
                continue;
            }

            $found = false;
            foreach ($remote_tutors as $remote_tutor) {
                if (!Utils::isValidDate($remote_tutor->getKursZugriffAb(), $remote_tutor->getKursZugriffBis())) {
                    $this->logger->debug(
                        'Ignoring tutor outside time frame: ' .
                        $remote_tutor->getDozentId()
                    );
                    continue;
                }
                if (Utils::compareOidsEqual($remote_tutor->getDozentId(), $tutor_oid)) {
                    $found = true;
                    break;
                }
            }
            foreach ($remote_companions as $remote_companion) {
                if (!Utils::isValidDate($remote_companion->getZustaendigAb(), $remote_companion->getZustaendigBis())) {
                    $this->logger->debug('Ignoring companion outside time frame: ' . $remote_companion->getLernbegleiterId());
                    continue;
                }
                if (Utils::compareOidsEqual($remote_companion->getLernbegleiterId(), $companion_oid)) {
                    $found = true;
                    break;
                }
            }
            foreach ($remote_supervisors as $remote_supervisor) {
                if (!Utils::isValidDate($remote_supervisor->getKursZugriffAb(), $remote_supervisor->getKursZugriffBis())) {
                    $this->logger->debug('Ignoring supervisor outside time frame: ' . $remote_supervisor->getAufsichtspersonId());
                    continue;
                }
                if (Utils::compareOidsEqual($remote_supervisor->getAufsichtspersonId(), $supervisor_oid)) {
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
        }
        // assign missing tutors
        foreach ($remote_tutors as $remote_tutor) {
            $tutor_oid = $remote_tutor->getDozentId();
            $this->logger->debug('Remote tutor oid is: ' . $tutor_oid);
            $this->logger->dump($this->udf_claiming_plugin->getUsersForTutorId($tutor_oid), ilLogLevel::DEBUG);

            foreach ($this->udf_claiming_plugin->getUsersForTutorId($tutor_oid) as $uid) {
                if (!in_array($uid, $participants->getTutors())) {
                    $this->rbac_admin->assignUser($course->getDefaultTutorRole(), $uid);
                    $participants->updateContact($uid, true);
                    $this->builder_factory->mailSegment()
                        ->withMessage('Remote tutor oid is: ' . $tutor_oid)
                        ->withType(MailType::MEMBERSHIP_UPDATED)
                        ->store();
                }
            }
        }
        // assign companions
        foreach ($remote_companions as $remote_companion) {
            $companion_id = $remote_companion->getLernbegleiterId();
            $this->logger->debug('Remote companion oid is: ' . $companion_id);
            $this->logger->dump($this->udf_claiming_plugin->getUsersForCompanionId($companion_id), ilLogLevel::DEBUG);

            if (!Utils::isValidDate($remote_companion->getZustaendigAb(), $remote_companion->getZustaendigBis())) {
                $this->logger->info('Outside time frame: Ignoring companion with id: ' . $companion_id);
                continue;
            }
            foreach ($this->udf_claiming_plugin->getUsersForCompanionId($companion_id) as $uid) {
                if (!in_array($uid, $participants->getTutors())) {
                    $message = 'Assigning new course tutor with id: ' . $companion_id . ' ILIAS id: ' . $uid;
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
        foreach ($remote_supervisors as $remote_supervisor) {
            $supervisor_id = $remote_supervisor->getAufsichtspersonId();
            $this->logger->debug('Remote supervisor oid is: ' . $supervisor_id);
            $this->logger->dump($this->udf_claiming_plugin->getUsersForSupervisorId($supervisor_id), ilLogLevel::DEBUG);

            if (!Utils::isValidDate($remote_supervisor->getKursZugriffAb(), $remote_supervisor->getKursZugriffBis())) {
                $this->logger->info('Outside time frame: Ignoring supervisor with id: ' . $supervisor_id);
                continue;
            }
            foreach ($this->udf_claiming_plugin->getUsersForSupervisorId($supervisor_id) as $uid) {
                if (!in_array($uid, $participants->getTutors())) {
                    $message = 'Assigning new course tutor with id: ' . $supervisor_id . ' ILIAS id: ' . $uid;
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
        return true;
    }

    protected function removeInvalidRegularMembers(
        ilObjCourse $course,
        MembersInterface $members
    ) : void {
        foreach ($this->rbac_review->assignedUsers($course->getDefaultMemberRole()) as $participant) {
            $oid = ilObjUser::_lookupImportId($participant);
            if (!$oid) {
                continue;
            }

            $found = false;
            foreach ($members as $member) {
                if (strtolower($member->getTeilnehmerId()) != strtolower($oid)) {
                    continue;
                }
                if (
                    $member->getMitgliedschaftsart() == self::REGULAR &&
                    !$member->getWechsel() &&
                    Utils::isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())
                ) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $message = 'Deassigning user: ' . $participant . ' with oid ' . $oid . ' from course: ' . $course->getTitle();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser(
                    $course->getDefaultMemberRole(),
                    $participant
                );
                $this->builder_factory->mailSegment()
                    ->withMessage($message)
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->store();
            }
        }
    }

    protected function removeInvalidPermanentSwitchMembers(
        ilObjCourse $course,
        MembersInterface $members,
        CourseStatusInterface $status
    ) : void {
        foreach ($this->rbac_review->assignedUsers($status->getPermanentSwitchRole()) as $participant) {
            $oid = ilObjUser::_lookupImportId($participant);
            if (!$oid) {
                $this->logger->debug('Ignoring non imported user.');
                continue;
            }

            $found = false;
            foreach ($members as $member) {
                if (strtolower($member->getTeilnehmerId()) != strtolower($oid)) {
                    continue;
                }
                if (
                    $member->getMitgliedschaftsart() == self::REGULAR &&
                    $member->getWechsel() &&
                    Utils::isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())
                ) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $message = 'Deassigning user: ' . $participant . ' with oid ' . $oid . ' from course: ' . $course->getTitle();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser(
                    $status->getPermanentSwitchRole(),
                    $participant
                );
                $this->builder_factory->mailSegment()
                    ->withMessage($message)
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->store();
            }
        }
    }

    protected function removeInvalidTemporarySwitchMembers(
        ilObjCourse $course,
        MembersInterface $members,
        CourseStatusInterface $status
    ) {
        foreach ($this->rbac_review->assignedUsers($status->getTemporarySwitchRole()) as $participant) {
            $oid = ilObjUser::_lookupImportId($participant);
            if (!$oid) {
                continue;
            }

            $found = false;
            foreach ($members as $member) {
                if (strtolower($member->getTeilnehmerId()) != strtolower($oid)) {
                    continue;
                }
                if ($member->getMitgliedschaftsart() == self::REGULAR && $member->getWechsel()) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $message = 'Deassigning user: ' . $participant . ' with oid ' . $oid . ' from course: ' . $course->getTitle();
                $this->logger->info($message);
                $this->rbac_admin->deassignUser(
                    $status->getTemporarySwitchRole(),
                    $participant
                );
                $this->builder_factory->mailSegment()
                    ->withMessage($message)
                    ->withType(MailType::MEMBERSHIP_UPDATED)
                    ->store();
            }
        }
    }

    protected function addRegularMembers(
        ilObjCourse $course,
        ilCourseParticipants $part,
        MembersInterface $members,
        array $assigned
    ) : void {
        foreach ($members as $member) {
            if ($member->getMitgliedschaftsart() != self::REGULAR) {
                $this->logger->debug('Ignoring TEMPORAER member.');
                continue;
            }
            if ($member->getWechsel()) {
                $this->logger->debug('Ignoring switch membership.');
                continue;
            }
            if (!Utils::isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())) {
                $this->logger->debug('Ignoring deprecated assignment.');
                continue;
            }

            $uid = $this->getUserIdForImportId($member->getTeilnehmerId());

            if ($uid) {
                $this->logger->info('Assigning user: ' . $uid . ' with oid ' . $member->getTeilnehmerId() . ' to course: ' . $course->getTitle());
                $this->assignUserToRole(
                    $course->getDefaultMemberRole(),
                    $uid,
                    $assigned,
                    $part,
                    $course
                );
            }
        }
    }

    /**
     * @param int[] $assigned
     */
    protected function addPermanentSwitchMembers(
        ilObjCourse $course,
        ilCourseParticipants $part,
        MembersInterface $members,
        CourseStatusInterface $status,
        array $assigned
    ) : void {
        foreach ($members as $member) {
            if ($member->getMitgliedschaftsart() != self::REGULAR) {
                $this->logger->debug('Ignoring TEMPORAER member.');
                continue;
            }
            if (!$member->getWechsel()) {
                $this->logger->debug('Ignoring regular membership.');
                continue;
            }
            if (!Utils::isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())) {
                $this->logger->debug('Ignoring deprecated assignment.');
                continue;
            }

            $uid = $this->getUserIdForImportId($member->getTeilnehmerId());

            if ($uid) {
                $this->logger->info('Assigning user: ' . $uid . ' with oid ' . $member->getTeilnehmerId() . ' to course: ' . $course->getTitle());
                $this->assignUserToRole(
                    $status->getPermanentSwitchRole(),
                    $uid,
                    $assigned,
                    $part,
                    $course
                );
            }
        }
    }

    /**
     * @param int[] $assigned
     */
    protected function addTemporarySwitchMembers(
        ilObjCourse $course,
        ilCourseParticipants $part,
        MembersInterface $members,
        CourseStatusInterface $status,
        array $assigned
    ) : void {
        foreach ($members as $member) {
            if ($member->getMitgliedschaftsart() == self::REGULAR) {
                $this->logger->debug('Ignoring permanent member.');
                continue;
            }
            if (!Utils::isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())) {
                $this->logger->debug('Ignoring deprecated assignment.');
                continue;
            }

            $uid = $this->getUserIdForImportId($member->getTeilnehmerId());

            if ($uid) {
                $this->assignUserToRole(
                    $status->getTemporarySwitchRole(),
                    $uid,
                    $assigned,
                    $part,
                    $course
                );
            }
        }
    }

    /**
     * @param int[] $assigned
     */
    protected function assignUserToRole(
        int $role,
        int $user,
        array &$assigned,
        ilCourseParticipants $part,
        ilObjCourse $course
    ) : void {
        $this->rbac_admin->assignUser($role, $user);
        if (!in_array($user, $assigned)) {
            $this->logger->debug('Adding new user sending mail notification...');
            $part->sendNotification(ilCourseMembershipMailNotification::TYPE_ADMISSION_MEMBER, $user);
            $favourites = new ilFavouritesManager();
            $favourites->add(
                $user,
                $course->getRefId()
            );
            $message = 'Assigning user: ' . $user . ' with role id ' . $role . ' to course: ' . $course->getTitle();
            $assigned[] = $user;
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::MEMBERSHIP_UPDATED)
                ->store();
        }
    }

    protected function getUserIdForImportId(
        ?string $oid
    ) : int {
        return ilObject::_lookupObjIdByImportId($oid);
    }
}
