<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingCourse;

use Leifos\VedaConnector\GeneratedOpenApi\Api\AusbildungsgngeApi;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface as ApiClientFactoryInterface;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface as ConfigFactoryInterface;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface as ApiHeaderFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\FactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ApiExceptionFactoryInterface $api_exception_factory,
        protected ApiHeaderFactoryInterface $api_header_factory,
        protected ConfigFactoryInterface $config_factory,
        protected ApiClientFactoryInterface $api_client_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        $config = $this->config_factory->openApi();
        $client = $this->api_client_factory->openApi();
        return new Handler(
            new AusbildungsgngeApi(
                $client,
                $config,
                $this->api_header_factory->openApi($config)
            ),
            $this->api_exception_factory
        );
    }
}
