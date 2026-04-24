<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Exception;

use Exception;

interface HandlerInterface
{
    public function writeToLog(
        Exception $e,
        string $api_call_name,
        string $access_token
    ): void;

    public function storeAsMailSegment(
        Exception $e,
        string $api_call_name,
        string $access_token
    ): void;
}
