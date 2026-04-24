<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Config;

use Leifos\VedaConnector\GeneratedOpenApi\Configuration;

interface FactoryInterface
{
    public function openApi(): Configuration;
}
