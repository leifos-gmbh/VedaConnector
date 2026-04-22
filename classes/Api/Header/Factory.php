<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Header;

use Leifos\VedaConnector\Api\OpenApiHeaderSelector;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\GeneratedOpenApi\HeaderSelector as OpenApiClientHeaderSelector;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected SettingsFactoryInterface $settings_factory
    ) {
    }

    public function openApi(
        Configuration $config
    ): OpenApiClientHeaderSelector
    {
        return new OpenApiHeaderSelector(
            $this->settings_factory->handler(),
            $config
        );
    }
}
