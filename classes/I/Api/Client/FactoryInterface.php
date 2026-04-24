<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Client;

use GuzzleHttp\Client as GClient;

interface FactoryInterface
{
    public function openApi(): GClient;
}
