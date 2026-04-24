<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram;

use Exception;
use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Api\AusbildungszgeApi;
use Leifos\VedaConnector\GeneratedOpenApi\Model\FehlermeldungApiDto;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\CompanionsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\FactoryInterface as CollectionsFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\MembersInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\SupervisorsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\TutorsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\HandlerInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsHandler;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;

class Handler implements HandlerInterface
{
    public const COURSE_CREATION_FAILED = 'Synchronisierung des Ausbildungszugs fehlgeschlagen.';

    protected string $plattform_id;

    public function __construct(
        protected AusbildungszgeApi $api,
        protected LoggerInterface $logger,
        protected SettingsHandler $settings,
        protected ApiExceptionFactoryInterface $api_exception_factory,
        protected CollectionsFactoryInterface $collections_factory
    ) {
        $this->plattform_id = $this->settings->read(SettingsName::PLATTFORM_ID);
    }

    protected function handleException(
        string $api_call_name,
        Exception $e
    ): void {
        $this->api_exception_factory->handler()->writeToLog($e, $api_call_name, $this->api->getConfig()->getAccessToken());
        $this->api_exception_factory->handler()->storeAsMailSegment($e, $api_call_name, $this->api->getConfig()->getAccessToken());
    }

    public function requestTutors(
        ?string $oid
    ) : ?TutorsInterface {
        try {
            $result = $this->api->getBeteiligteDozentenVonAusbildungszugUsingGET($oid);
            $this->logger->debug('Reveived tutors of education train with id: ' . $oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->tutors(...$result);
        } catch (Exception $e) {
            $this->handleException('getBeteiligteDozentenVonAusbildungszugUsingGET', $e);
            return null;
        }
    }

    public function requestCompanions(
        ?string $oid
    ) : ?CompanionsInterface {
        try {
            $result = $this->api->getLernbegleiterVonAusbildungszugUsingGET($oid);
            $this->logger->debug('Received companions of education train with id: ' . $oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->companions(...$result);
        } catch (Exception $e) {
            $this->handleException('getLernbegleiterVonAusbildungszugUsingGET', $e);
            return null;
        }
    }

    public function requestSupervisors(
        ?string $oid
    ) : ?SupervisorsInterface {
        try {
            $result = $this->api->getAufsichtspersonenVonAusbildungszugUsingGET($oid);
            $this->logger->debug('Received supervisors of education train with id: ' . $oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->supervisors(...$result);
        } catch (Exception $e) {
            $this->handleException('getAufsichtspersonenVonAusbildungszugUsingGET', $e);
            return null;
        }
    }

    public function requestMembers(
        ?string $oid
    ) : ?MembersInterface {
        try {
            $result = $this->api->getTeilnehmerVonAusbildungszugUsingGET($oid);
            $this->logger->debug('Received members of education train with id: ' . $oid);
            $this->logger->dump($result, ilLogLevel::DEBUG);
            return $this->collections_factory->members(...$result);
        } catch (Exception $e) {
            $this->handleException('getTeilnehmerVonAusbildungszugUsingGET', $e);
            return null;
        }
    }

    public function sendCourseCreationFailed(
        string $oid
    ) : bool {
        try {
            $error_message = new FehlermeldungApiDto();
            $error_message->setFehlermeldung(self::COURSE_CREATION_FAILED);
            $this->api->meldeAusbildungszugAnlageFehlgeschlagenUsingPOST(
                $oid,
                $error_message
            );
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeAusbildungszugAnlageFehlgeschlagenUsingPOST', $e);
            return false;
        }
    }

    public function sendCourseCreated(
        string $oid
    ) : bool {
        try {
            $this->api->meldeAusbildungszugAlsExternExistierendUsingPOST($oid);
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeAusbildungszugAlsExternExistierendUsingPOST', $e);
            return false;
        }
    }

    public function sendCopyStarted(
        string $oid
    ) : bool {
        try {
            $this->api->meldeExterneAnlageAngestossenUsingPOST($oid);
            return true;
        } catch (Exception $e) {
            $this->handleException('meldeExterneAnlageAngestossenUsingPOST', $e);
            return false;
        }
    }
}
