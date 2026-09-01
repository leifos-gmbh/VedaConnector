<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api;

use ILIAS\DI\Exceptions\Exception;
use ilLogLevel;
use ilLPStatus;
use ilObjCourse;
use ilObjUser;
use ilUserCertificate;
use Leifos\VedaConnector\I\Api\Adapter\CourseImportInterface as CourseImportAdapterInterface;
use Leifos\VedaConnector\I\Api\Adapter\CourseStandardImportInterface as CourseStandardImportAdapterInterface;
use Leifos\VedaConnector\I\Api\Adapter\MemberImportInterface as MemberImportAdapterInterface;
use Leifos\VedaConnector\I\Api\Adapter\MemberStandardImportInterface as MemberStandardImportAdapterInterface;
use Leifos\VedaConnector\I\Api\Adapter\UserImportInterface as UserImportAdapterInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\HandlerInterface as ELearningPlattformApiInterface;
use Leifos\VedaConnector\I\Api\HandlerInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\HandlerInterface as TrainingCourseApiInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\HandlerInterface as TrainingProgramApiInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status as CourseStatusStatus;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type as CourseStatusType;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface as CourseStatusDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type as MailType;
use Leifos\VedaConnector\I\MDClaiming\DB\HandlerInterface as MDClaimingDBInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;
use Leifos\VedaConnector\I\PDFSendStatus\FactoryInterface as PDFSendStatusFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status as UserStatusStatus;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface as UserStatusDBInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface as UtilsInterface;

readonly class Handler implements HandlerInterface
{
    protected const REMOTE_SESSION_TYPE = 'Präsenz';
    protected const REMOTE_EXERCISE_TYPE = 'Selbstlernen';

    public function __construct(
        protected CourseImportAdapterInterface $course_import_adapter,
        protected CourseStandardImportAdapterInterface $course_standard_import_adapter,
        protected MemberImportAdapterInterface $member_import_adapter,
        protected MemberStandardImportAdapterInterface $member_standard_import_adapter,
        protected UserImportAdapterInterface $user_import_adapter,
        protected BuilderFactoryInterface $builder_factory,
        protected LoggerInterface $logger,
        protected MDClaimingDBInterface $md_claiming_db,
        protected UserStatusDBInterface $user_status_db,
        protected CourseStatusDBInterface $course_status_db,
        protected ELearningPlattformApiInterface $elearning_plattform_api,
        protected TrainingProgramApiInterface $training_program_api,
        protected TrainingCourseApiInterface $training_course_api,
        protected UtilsInterface $utils,
        protected PDFSendStatusFactoryInterface $pdf_send_status_factory
    ) {
    }

    public function handleAfterCloningDependenciesSIFAEvent(int $source_id, int $target_id, int $copy_id) : void
    {
        $this->course_import_adapter->handleAfterCloningDependenciesEvent(
            $source_id,
            $target_id,
            $copy_id
        );
    }

    public function handleAfterCloningDependenciesStandardEvent(int $source_id, int $target_id, int $copy_id) : void
    {
        $this->course_standard_import_adapter->handleAfterCloningDependenciesEvent(
            $source_id,
            $target_id,
            $copy_id
        );
    }

    public function handleAfterCloningSIFAEvent(int $a_source_id, int $a_target_id, int $a_copy_id) : void
    {
        $this->course_import_adapter->handleAfterCloningEvent(
            $a_source_id,
            $a_target_id,
            $a_copy_id
        );
    }

    public function handleAfterCloningStandardEvent(int $a_source_id, int $a_target_id, int $a_copy_id) : void
    {
        $this->course_standard_import_adapter->handleAfterCloningEvent(
            $a_source_id,
            $a_target_id,
            $a_copy_id
        );
    }

    protected function handleTrackingEventDokumentSuccess(int $obj_id, int $usr_id, int $status)
    {
        $this->logger->debug(
            'Handling tracking event to document success (obj_id, user_id, status): ('
            . $obj_id . ', '
            . $usr_id . ', '
            . $status . ')'
        );
        $crs_oid = null;
        $usr_oid = null;

        if (
            $status !== ilLPStatus::LP_STATUS_COMPLETED_NUM &&
            $status !== ilLPStatus::LP_STATUS_FAILED_NUM
        ) {
            $this->logger->debug('Ignoring every learning progress status except: failed, completed');
            return;
        }
        if (!ilObjCourse::_exists($obj_id)) {
            $this->logger->debug('Course with id does not exist: ' . $obj_id);
            return;
        }
        if (!ilObjUser::_exists($usr_id)) {
            $this->logger->debug('User with id does not exist: ' . $usr_id);
            return;
        }
        if (
            is_null(($usr_oid = ilObjUser::_lookupImportId($usr_id))) ||
            $usr_oid == ''
        ) {
            $this->logger->debug('User oid is null or empty.');
            return;
        }
        if (
            is_null(($crs_oid = ilObjCourse::_lookupImportId($obj_id))) ||
            $crs_oid == ''
        ) {
            $this->logger->debug('Course oid is null or empty.');
            return;
        }

        $veda_usr = $this->user_status_db->lookupByOId($usr_oid);
        $veda_crs = $this->course_status_db->lookupByOId($crs_oid);

        if (is_null($veda_usr)) {
            $this->logger->debug('User with oid does not exist: ' . $crs_oid);
            return;
        }
        if (is_null($veda_crs)) {
            $this->logger->debug('Course with oid does not exist: ' . $crs_oid);
            return;
        }
        if (!$veda_crs->getDocumentSuccess()) {
            $this->logger->debug('Document success is not enabled for course with oid:' . $crs_oid);
            return;
        }

        if ($status === ilLPStatus::LP_STATUS_FAILED_NUM) {
            $this->logger->info('Send usr: ' . $usr_oid . ' failed crs: ' . $crs_oid);
            $this->elearning_plattform_api->sendCourseFailed($crs_oid, $usr_oid);
            return;
        }

        if ($status === ilLPStatus::LP_STATUS_COMPLETED_NUM) {
            $this->logger->info('Send usr: ' . $usr_oid . ' passed crs: ' . $crs_oid);
            $this->elearning_plattform_api->sendCoursePassed($crs_oid, $usr_oid);
        }
    }

    protected function handleTrackingEventStartCourseWork(int $obj_id, int $usr_id, int $status)
    {
        $this->logger->debug(
            'Start handling participant started working on course (obj_id, user_id, status): ('
            . $obj_id . ', '
            . $usr_id . ', '
            . $status . ')'
        );

        if ($status != ilLPStatus::LP_STATUS_IN_PROGRESS_NUM) {
            $this->logger->debug('Ignoring every learning progress status except: in progress');
            return;
        }

        $veda_crs = $this->course_status_db->lookupById($obj_id);
        $veda_usr = $this->user_status_db->lookupById($usr_id);

        if (is_null($veda_crs) || is_null($veda_usr)) {
            $this->logger->debug('handleParticipantAssignedToCourse, null course or user');
            return;
        }
        if (is_null($veda_crs->getOid()) || is_null($veda_usr->getOid())) {
            $this->logger->debug('handleParticipantAssignedToCourse, null course_oid or user_oid');
            return;
        }
        if (!$veda_crs->getDocumentSuccess()) {
            $this->logger->info('Ignore course without document success flag');
            return;
        }

        $this->logger->info('Send usr:' . $veda_usr->getOid() . ' started working on crs:' . $veda_crs->getOid());
        $this->elearning_plattform_api->sendParticipantStartedCourseWork(
            $veda_crs->getOid(),
            $veda_usr->getOid()
        );
    }

    public function handleTrackingEvent(int $obj_id, int $usr_id, int $status) : void
    {
        $this->handleTrackingEventStartCourseWork(
            $obj_id,
            $usr_id,
            $status
        );

        $this->handleTrackingEventDokumentSuccess(
            $obj_id,
            $usr_id,
            $status
        );

        $this->member_import_adapter->handleTrackingEvent(
            $obj_id,
            $usr_id,
            $status
        );
    }

    public function handleCertificateIssuedEvent(
        ilUserCertificate $certificate
    ): void {
        $this->logger->debug(sprintf(
            'Start handling certificate issued event (obj_id, user_id, cert_id): (%s, %s, %s)',
            $certificate->getObjId(),
            $certificate->getUserId(),
            $certificate->getId()
        ));

        $veda_crs = $this->course_status_db->lookupById($certificate->getObjId());
        $veda_usr = $this->user_status_db->lookupById($certificate->getUserId());

        if (is_null($veda_crs) || is_null($veda_usr)) {
            $this->logger->debug('handleCertificateIssuedEvent, null course or user');
            return;
        }
        if (is_null($veda_crs->getOid()) || is_null($veda_usr->getOid())) {
            $this->logger->debug('handleCertificateIssuedEvent, null course_oid or user_oid');
            return;
        }

        $passed_date = new \DateTimeImmutable();
        $passed_date = $passed_date->setTimestamp($certificate->getAcquiredTimestamp());
        $pdf_send_status = $this->pdf_send_status_factory->db()->handler()->createElement()
            ->withSendStatus(SendStatus::NOT_SEND)
            ->withErrorCode(ErrorCode::NO_ERROR)
            ->withPassedDate($passed_date)
            ->withPassedStatus(PassedStatus::PASSED)
            ->withParticipantOId($veda_crs->getOid())
            ->withCourseOId($veda_usr->getOid())
            ->withCourseId($certificate->getObjId())
            ->withParticipantId($certificate->getUserId());

        $this->pdf_send_status_factory->db()->handler()->updateByElement($pdf_send_status);
    }

    public function handlePasswordChanged(int $usr_id) : void
    {
        $import_id = ilObjUser::_lookupImportId($usr_id);

        if (!$import_id) {
            $this->logger->debug('No import id for user ' . $usr_id);
            return;
        }

        $veda_user = $this->builder_factory->userStatus()
            ->withOID($import_id)
            ->get();

        if (
            $veda_user->isImportFailure() ||
            $veda_user->getPasswordStatus() != UserStatusStatus::PENDING
        ) {
            $this->logger->debug('No password notification required.');
        }

        $this->elearning_plattform_api->sendFirstLoginSuccess($veda_user->getOid());

        $this->builder_factory->userStatus()
            ->withOID($import_id)
            ->withPasswordStatus(UserStatusStatus::SYNCHRONIZED)
            ->store();
    }

    public function deleteDeprecatedILIASUsers() : void
    {
        $participants = $this->elearning_plattform_api->requestParticipants(false);
        foreach ($this->user_status_db->lookupAll() as $user) {
            $found_remote = false;
            if (is_null($participants)) {
                continue;
            }
            foreach ($participants as $participant) {
                if ($this->utils->compareOidsEqual($user->getOid(), $participant->getTeilnehmer()->getOid())) {
                    $found_remote = true;
                }
            }
            if (!$found_remote) {
                $this->user_status_db->deleteByOId($user->getOid());
            }
        }
    }

    public function handleCloningFailed() : void
    {
        $failed = $this->course_status_db->lookupAll()->getSubCollectionOfAsynchronusElements();
        foreach ($failed as $fail) {
            $oid = $fail->getOid();
            $message = '';
            $this->logger->notice('Handling failed clone event for oid: ' . $fail->getOid());
            if (
                $fail->getType() == CourseStatusType::SIFA
            ) {
                $this->training_program_api->sendCourseCreationFailed($oid);
                $message = 'SIFA course cloning failed, course oid: ' . $fail->getOid();
            } elseif (
                $fail->getType() == CourseStatusType::STANDARD
            ) {
                $this->elearning_plattform_api->sendCourseCreationFailed(
                    $oid,
                    'Synchronisierung des ELearning-Kurses fehlgeschlagen.'
                );
                $message = 'Standard course cloning failed, course oid: ' . $fail->getOid();
            } else {
                $message = 'Unknown course cloning failed, course oid: ' . $fail->getOid();
                $this->logger->error('Unknown type given for oid ' . $fail->getOid());
            }
            $this->builder_factory->courseStatus()
                ->withOID($fail->getOid())
                ->withModified(time())
                ->withStatusCreated(CourseStatusStatus::FAILED)
                ->store();

            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
        }
    }

    public function importILIASUsersStandard(bool $a_incremental = false) : void
    {
        $participants = $this->elearning_plattform_api->requestParticipants($a_incremental);
        if (is_null($participants)) {
            return;
        }
        $this->user_import_adapter->import($participants, UserImportAdapterInterface::IMPORT_MODE_STANDARD);
    }

    public function importILIASUsersSIFA(bool $a_incremental = false) : void
    {
        $participants = $this->elearning_plattform_api->requestParticipants($a_incremental);
        if (is_null($participants)) {
            return;
        }
        $this->user_import_adapter->import($participants, UserImportAdapterInterface::IMPORT_MODE_SIFA);
    }

    public function importStandardCourses() : void
    {
        $this->course_standard_import_adapter->import();
    }

    public function importSIFACourses() : void
    {
        $this->course_import_adapter->import();
    }

    public function importSIFAMembers() : void
    {
        $this->member_import_adapter->import();
    }

    public function importStandardMembers() : void
    {
        $this->member_standard_import_adapter->import();
    }

    public function isTrainingCourseValid($course_oid) : bool
    {
        $training_course = $this->training_course_api->getCourse($course_oid);
        if (!is_null($training_course)) {
            $this->logger->dump($training_course, ilLogLevel::DEBUG);
        }
        return !is_null($training_course);
    }

    public function validateLocalSessions(array $sessions, string $course_oid) : array
    {
        $missing = [];
        $training_course = $this->training_course_api->getCourse($course_oid);

        if (is_null($training_course)) {
            return $missing;
        }

        foreach ($sessions as $index => $node) {
            if (!$node['vedaid']) {
                continue;
            }
            $local_id = $node['vedaid'];
            $found_remote = false;
            foreach ($training_course->getAusbildungsgangabschnitte() as $segment) {
                if (!$segment->getAbbildungAufELearningPlattform()) {
                    $this->logger->debug('Ignoring of type: !AbbildungAufELearningPlattform');
                    continue;
                }

                if ($segment->getAusbildungsgangabschnittsart() != self::REMOTE_SESSION_TYPE) {
                    $this->logger->debug('Ignoring type: ' . $segment->getAusbildungsgangabschnittsart());
                    continue;
                }

                $remote_id = $segment->getOid();
                if ($this->utils->compareOidsEqual($local_id, $remote_id)) {
                    $found_remote = true;
                    break;
                }
            }
            if (!$found_remote) {
                $missing[] = $node;
            }
        }
        return $missing;
    }

    public function validateRemoteSessions(array $sessions, string $course_oid) : array
    {
        $missing = [];
        $training_course = $this->training_course_api->getCourse($course_oid);

        if (is_null($training_course)) {
            return $missing;
        }

        foreach ($training_course->getAusbildungsgangabschnitte() as $segment) {
            if (!$segment->getAbbildungAufELearningPlattform()) {
                $this->logger->debug('Ignoring of type: !AbbildungAufELearningPlattform');
                continue;
            }

            if ($segment->getAusbildungsgangabschnittsart() != self::REMOTE_SESSION_TYPE) {
                $this->logger->debug('Ignoring segment of type: ' . $segment->getAusbildungsgangabschnittsart());
                continue;
            }
            $found_local = false;
            foreach ($sessions as $index => $node) {
                $local_id = $node['vedaid'];
                $remote_id = $segment->getOid();
                if ($this->utils->compareOidsEqual($local_id, $remote_id)) {
                    $found_local = true;
                    break;
                }
            }
            if (!$found_local) {
                $missing[$segment->getOid()] = $segment->getBezeichnung();
            }
        }
        return $missing;
    }

    public function validateLocalExercises(array $exercises, string $course_oid) : array
    {
        $missing = [];
        $training_course = $this->training_course_api->getCourse($course_oid);

        if (is_null($training_course)) {
            return $missing;
        }

        foreach ($exercises as $index => $node) {
            if (!$node['vedaid']) {
                continue;
            }
            $local_id = $node['vedaid'];
            $found_remote = false;
            foreach ($training_course->getAusbildungsgangabschnitte() as $segment) {
                if (!$segment->getAbbildungAufELearningPlattform()) {
                    $this->logger->debug('Ignoring of type: !AbbildungAufELearningPlattform');
                    continue;
                }
                $remote_id = $segment->getOid();
                if ($this->utils->compareOidsEqual($local_id, $remote_id)) {
                    $found_remote = true;
                    break;
                }
            }
            if (!$found_remote) {
                $missing[] = $node;
            }
        }
        return $missing;
    }

    public function validateRemoteExercises(array $exercises, string $course_oid) : array
    {
        $missing = [];
        $training_course = $this->training_course_api->getCourse($course_oid);

        if (is_null($training_course)) {
            return $missing;
        }

        foreach ($training_course->getAusbildungsgangabschnitte() as $segment) {
            if (!$segment->getAbbildungAufELearningPlattform()) {
                $this->logger->debug('Ignoring segment of type: !AbbildungAufELearningPlattform');
                continue;
            }

            if ($segment->getAusbildungsgangabschnittsart() == self::REMOTE_SESSION_TYPE) {
                $this->logger->debug('Ignoring segment of type: ' . $segment->getAusbildungsgangabschnittsart());
                continue;
            }
            $found_local = false;
            foreach ($exercises as $index => $node) {
                $local_id = $node['vedaid'];
                $remote_id = $segment->getOid();
                if ($this->utils->compareOidsEqual($local_id, $remote_id)) {
                    $found_local = true;
                    break;
                }
            }
            if (!$found_local) {
                $missing[$segment->getOid()] = $segment->getBezeichnung();
            }
        }

        return $missing;
    }

    public function testConnection() : bool
    {
        if (!is_null($this->elearning_plattform_api->requestParticipants())) {
            $id = $this->md_claiming_db->findTrainingCourseId(70);
            $this->logger->notice($id . ' is the training course id');
            return true;
        }
        return false;
    }
}
