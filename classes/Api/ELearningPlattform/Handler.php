<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ELearningPlattform;

use Exception;
use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Api\ELearningPlattformenApi;
use Leifos\VedaConnector\GeneratedOpenApi\Model\FehlermeldungApiDto;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseCompanionsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseMembersInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CoursesInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseTutorsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\FactoryInterface as ELearningPlattformCollectionsFactoryInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\TrainingProgramCoursesInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\HandlerInterface;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsHandler;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;

class Handler implements HandlerInterface
{
    protected ELearningPlattformenApi $api_elearning;
    protected string $plattform_id;

    public function __construct(
        protected ELearningPlattformCollectionsFactoryInterface $collections_factory,
        protected ELearningPlattformenApi $api,
        protected LoggerInterface $logger,
        protected SettingsHandler $settings,
        protected ApiExceptionFactoryInterface $exception_factory
    ) {
        $this->plattform_id = $this->settings->read(SettingsName::PLATTFORM_ID);
    }

    protected function handleException(
        string $api_call_name,
        Exception $e
    ): void {
        $this->exception_factory->handler()->writeToLog($e, $api_call_name, $this->api_elearning->getConfig()->getAccessToken());
        $this->exception_factory->handler()->storeAsMailSegment($e, $api_call_name, $this->api_elearning->getConfig()->getAccessToken());
    }

    public function requestCourseMembers(
        string $crs_oid
    ) : ?CourseMembersInterface {
        try {
            $result = $this->api_elearning->getVonTeilnehmernDieAktivenKurszuordnungenUsingGET(
                $this->plattform_id,
                $crs_oid
            );
            $this->logger->debug('Received course members of course with oid: ' . $crs_oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->courseMembers(...$result);
        } catch (Exception $e) {
            $this->handleException('getVonTeilnehmernDieAktivenKurszuordnungenUsingGET', $e);
            return null;
        }
    }

    public function requestCourseCompanions(
        string $crs_oid
    ) : ?CourseCompanionsInterface {
        try {
            $result = $this->api_elearning->getVonLernbegleiternDieAktivenKurszuordnungenUsingGET(
                $this->plattform_id,
                $crs_oid
            );
            $this->logger->debug('Received course companions of course with oid: ' . $crs_oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->courseCompanions(...$result);
        } catch (Exception $e) {
            $this->handleException('getVonLernbegleiternDieAktivenKurszuordnungenUsingGET', $e);
            return null;
        }
    }

    public function requestCourseTutors(
        string $crs_oid
    ) : ?CourseTutorsInterface {
        try {
            $result = $this->api_elearning->getVonDozentenDieAktivenKurszuordnungenUsingGET(
                $this->plattform_id,
                $crs_oid
            );
            $this->logger->debug('Received course tutors of course with oid: ' . $crs_oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->courseTutors(...$result);
        } catch (Exception $e) {
            $this->handleException('getVonDozentenDieAktivenKurszuordnungenUsingGET', $e);
            return null;
        }
    }

    public function requestCourses() : ?CoursesInterface
    {
        try {
            $result = $this->api_elearning->getAktiveELearningKurseUsingGET(
                $this->plattform_id
            );
            $this->logger->debug('Received e-learning courses.');
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->courses(...$result);
        } catch (Exception $e) {
            $this->handleException('getAktiveELearningKurseUsingGET', $e);
            return null;
        }
    }

    public function requestTrainingCourseTrains(
        string $training_course_id
    ) : ?TrainingProgramCoursesInterface {
        try {
            $result = $this->api_elearning->getFreigegebeneAusbildungszuegeFuerPlattformUndAusbildungsgangUsingGET(
                $this->plattform_id,
                $training_course_id
            );
            $this->logger->debug('Received education trains with training course id: ' . $training_course_id);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->trainingProgramCourses(...$result);
        } catch (Exception $e) {
            $this->handleException('getFreigegebeneAusbildungszuegeFuerPlattformUndAusbildungsgangUsingGET', $e);
            return null;
        }
    }

    public function requestParticipants(
        bool $a_incremental = false
    ) : ?ParticipantsInterface {
        try {
            $result = $this->api_elearning->getTeilnehmerELearningPlattformUsingGET($this->plattform_id);#, $a_incremental);
            if ($a_incremental) {
                $this->logger->info('Received new participants.');
            } else {
                $this->logger->debug('Received all participants.');
            }
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->participants(...$result);
        } catch (Exception $e) {
            $this->handleException('getTeilnehmerELearningPlattformUsingGET', $e);
            return null;
        }
    }

    public function sendCourseCopyStarted(
        string $crs_oid
    ) : bool {
        try {
            $this->api_elearning->meldeElearningkursExterneAnlageAngestossenUsingPOST(
                $this->plattform_id,
                $crs_oid
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeElearningkursExterneAnlageAngestossenUsingPOST', $e);
            return false;
        }
    }

    public function sendCourseCreationFailed(
        string $crs_oid,
        string $message
    ) : bool {
        try {
            $error_message = new FehlermeldungApiDto();
            $error_message->setFehlermeldung($message);
            $this->api_elearning->meldeElearningkursExterneAnlageFehlgeschlagenUsingPOST(
                $this->plattform_id,
                $crs_oid,
                $error_message
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeElearningkursExterneAnlageFehlgeschlagenUsingPOST', $e);
            return false;
        }
    }

    public function sendCourseCreated(
        string $crs_oid
    ) : bool {
        try {
            $this->api_elearning->meldeElearningkursExternExistierendUsingPOST(
                $this->plattform_id,
                $crs_oid
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeElearningkursExternExistierendUsingPOST', $e);
            return false;
        }
    }

    public function sendParticipantStartedCourseWork(
        string $crs_oid,
        string $usr_oid
    ) : bool {
        try {
            $this->api_elearning->meldeBearbeitungsstartFuerTeilnehmerAufKursUsingPOST(
                $this->plattform_id,
                $crs_oid,
                $usr_oid
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeBearbeitungsstartFuerTeilnehmerAufKursUsingPOST', $e);
            return false;
        }
    }

    public function sendAccountCreated(
        string $participant_id
    ) : bool {
        try {
            $this->api_elearning->meldeElearningaccountAlsExternExistierendUsingPOST(
                $this->plattform_id,
                $participant_id
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeElearningaccountAlsExternExistierendUsingPOST', $e);
            return false;
        }
    }

    public function sendAccountCreationFailed(
        string $usr_oid,
        string $message
    ) : bool {
        try {
            $error_message = new FehlermeldungApiDto();
            $error_message->setFehlermeldung($message);
            $this->api_elearning->meldeElearningaccountAnlageAlsFehlgeschlagenUsingPOST(
                $this->plattform_id,
                $usr_oid,
                $error_message
            );
            $this->logger->info('Send message: ' . $error_message->getFehlermeldung());
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeElearningaccountAnlageAlsFehlgeschlagen', $e);
            return false;
        }
    }

    public function sendCoursePassed(
        string $crs_oid,
        string $usr_oid
    ) : bool {
        try {
            $this->api_elearning->meldeKursabschlussMitErfolgUsingPOST(
                $this->plattform_id,
                $crs_oid,
                $usr_oid,
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeKursabschlussMitErfolgUsingPOST', $e);
            return false;
        }
    }

    public function sendCourseFailed(
        string $crs_oid,
        string $usr_oid
    ) : bool {
        try {
            $this->api_elearning->meldeKursabschlussOhneErfolgUsingPOST(
                $this->plattform_id,
                $crs_oid,
                $usr_oid
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeKursabschlussOhneErfolgUsingPOST', $e);
            return false;
        }
    }

    public function sendFirstLoginSuccess(
        string $usr_oid
    ) : bool {
        try {
            $this->api_elearning->meldeErstmaligErfolgreichEingeloggtUsingPOST(
                $this->plattform_id,
                $usr_oid
            );
            $this->logger->info('Password notification sent.');
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeErstmaligErfolgreichEingeloggt', $e);
            return false;
        }
    }
}
