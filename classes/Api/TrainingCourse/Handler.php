<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingCourse;

use Exception;
use Leifos\VedaConnector\GeneratedOpenApi\Api\AusbildungsgngeApi;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungsgang;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\HandlerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        protected AusbildungsgngeApi $api,
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

    public function getCourse(string $training_course_id) : ?Ausbildungsgang
    {
        try {
            return $this->api->getAusbildungsgangUsingGET($training_course_id);
        } catch (Exception $e) {
            $this->handleException('getAusbildungsgangUsingGET', $e);
        }
        return null;
    }
}
