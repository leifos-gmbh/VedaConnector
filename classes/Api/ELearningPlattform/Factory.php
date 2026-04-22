<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ElearningPlattform;

use GuzzleHttp\Client as GClient;
use Leifos\VedaConnector\Api\ElearningPlattform\Collections\Factory as CollectionsFactory;
use Leifos\VedaConnector\GeneratedOpenApi\Api\ELearningPlattformenApi;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\FactoryInterface as CollectionsFactoryInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\FactoryInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\HandlerInterface;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface as ApiHeaderFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface as ConfigFactoryInterface;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface as ApiClientFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected ApiExceptionFactoryInterface $api_exception_factory,
        protected ApiHeaderFactoryInterface $api_header_factory,
        protected ConfigFactoryInterface $config_factory,
        protected ApiClientFactoryInterface $api_client_factory
    ) {
    }

    public function handler(): HandlerInterface {
        $config = $this->config_factory->openApi();
        $client = $this->api_client_factory->openApi();
        return new Handler(
            $this->collections(),
            new ELearningPlattformenApi(
                $client,
                $config,
                $this->api_header_factory->openApi($config)
            ),
            $this->logger_factory->handler(),
            $this->settings_factory->handler(),
            $this->api_exception_factory
        );
    }

    public function collections(): CollectionsFactoryInterface
    {
        return new CollectionsFactory(
            $this->logger_factory
        );
    }
}
