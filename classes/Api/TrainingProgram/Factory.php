<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram;

use Leifos\VedaConnector\Api\TrainingProgram\Collections\Factory as CollectionsFactory;
use Leifos\VedaConnector\GeneratedOpenApi\Api\AusbildungszgeApi;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface as ApiClientFactoryInterface;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface as ConfigFactoryInterface;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface as ApiHeaderFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\FactoryInterface as CollectionsFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\FactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\HandlerInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected ApiExceptionFactoryInterface $api_exception_factory,
        protected ApiHeaderFactoryInterface $api_header_factory,
        protected ConfigFactoryInterface $config_factory,
        protected ApiClientFactoryInterface $client_factory
    ) {
    }

    public function handler(): HandlerInterface {
        $config = $this->config_factory->openApi();
        $client = $this->client_factory->openApi();
        return new Handler(
            new AusbildungszgeApi(
                $client,
                $config,
                $this->api_header_factory->openApi($config)
            ),
            $this->logger_factory->handler(),
            $this->settings_factory->handler(),
            $this->api_exception_factory,
            $this->collections()
        );
    }

    public function collections(): CollectionsFactoryInterface
    {
        return new CollectionsFactory(
            $this->logger_factory
        );
    }
}
