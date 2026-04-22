<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgramModules;

use DateTime;
use Exception;
use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Api\AusbildungszugabschnitteApi;
use Leifos\VedaConnector\GeneratedOpenApi\Model\MeldeLernerfolgApiDto;
use Leifos\VedaConnector\GeneratedOpenApi\Model\PraktikumsberichtEingegangenApiDto;
use Leifos\VedaConnector\GeneratedOpenApi\Model\PraktikumsberichtKorrigiertApiDto;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgramModules\HandlerInterface as ApiTrainingProgramModulesInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Handler implements ApiTrainingProgramModulesInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected AusbildungszugabschnitteApi $api,
        protected ApiExceptionFactoryInterface $api_exception_factory
    ) {
    }

    protected function handleException(
        string $api_call_name,
        Exception $e
    ): void {
        $this->api_exception_factory->handler()->writeToLog($e, $api_call_name, $this->api->getConfig()->getAccessToken());
        $this->api_exception_factory->handler()->storeAsMailSegment($e, $api_call_name, $this->api->getConfig()->getAccessToken());
    }

    public function sendExerciseSubmissionConfirmed(
        string $segment_oid,
        string $participant_oid,
        ?DateTime $confirmed = null
    ) : bool {
        try {
            $info = new PraktikumsberichtKorrigiertApiDto();
            if (!is_null($confirmed)) {
                $info->setPraktikumsberichtKorrigiert(true);
                $info->setPraktikumsberichtKorrigiertAm($confirmed);
            } else {
                $info->setPraktikumsberichtKorrigiert(false);
            }
            $this->logger->dump($info, ilLogLevel::DEBUG);
            $this->api->meldePraktikumsberichtKorrigiertUsingPUT($segment_oid, $participant_oid, $info);
            return true;
        } catch (Exception $e) {
            $this->handleException('meldePraktikumsberichtKorrigiertUsingPUT', $e);
            return false;
        }
    }

    public function sendExerciseSubmissionDate(
        string $segment_oid,
        string $participant_oid,
        ?DateTime $subdate = null
    ) : bool {
        try {
            $info = new PraktikumsberichtEingegangenApiDto();
            if (!is_null($subdate)) {
                $info->setPraktikumsberichtEingegangen(true);
                $info->setPraktikumsberichtEingegangenAm($subdate);
            } else {
                $info->setPraktikumsberichtEingegangen(false);
            }
            $this->logger->dump($info, ilLogLevel::DEBUG);
            $this->api->meldePraktikumsberichtEingegangenUsingPUT($segment_oid, $participant_oid, $info);
            return true;
        } catch (Exception $e) {
            $this->handleException('meldePraktikumsberichtEingegangenUsingPUT', $e);
            return false;
        }
    }

    public function sendExerciseSuccess(
        string $segment_oid,
        string $participant_oid,
        DateTime $dt
    ) : bool {
        try {
            $info = new MeldeLernerfolgApiDto();
            $info->setLernerfolg(true);
            $info->setLernerfolgGemeldetAm($dt);
            $this->logger->dump($info, ilLogLevel::DEBUG);
            $this->api->meldeLernerfolgUsingPUT($segment_oid, $participant_oid, $info);
            return true;
        } catch (Exception $e) {
            $this->handleException('MeldeLernerfolgApiDto', $e);
            return false;
        }
    }
}
