<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Header;

use Leifos\VedaConnector\GeneratedOpenApi\HeaderSelector as OpenApiClientHeaderSelector;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;

interface FactoryInterface
{
    public function openApi(
        Configuration $config
    ): OpenApiClientHeaderSelector;
}
