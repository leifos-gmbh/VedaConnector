<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Client;

use GuzzleHttp\Client as GClient;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface;

class Factory implements FactoryInterface
{
    public function openApi(): GClient
    {
        return new GClient();
    }
}
