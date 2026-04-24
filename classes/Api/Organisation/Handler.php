<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Organisation;

use Exception;
use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Api\OrganisationenApi;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Organisation;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\Organisation\HandlerInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        protected OrganisationenApi $api,
        protected LoggerInterface $logger,
        protected ApiExceptionFactoryInterface $api_exception_factory
    ) {
    }

    protected function handleException(string $api_call_name, Exception $e): void
    {
        $this->api_exception_factory->handler()->writeToLog($e, $api_call_name, $this->api->getConfig()->getAccessToken());
        $this->api_exception_factory->handler()->storeAsMailSegment($e, $api_call_name, $this->api->getConfig()->getAccessToken());
    }

    public function getOrganisation(string $orgr_oid) : ?Organisation
    {
        try {
            $response = $this->api->getOrganisationUsingGET($orgr_oid);
            $this->logger->dump($response, ilLogLevel::DEBUG);
            return $response;
        } catch (Exception $e) {
            $this->handleException('getOrganisationUsingGET', $e);
        }
        return null;
    }
}
