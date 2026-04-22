<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Logger;

use ilLogLevel;

interface HandlerInterface
{
    public function debug(
        string $message
    ): void;

    public function info(
        string $message
    ): void;

    public function error(
        string $message
    ): void;

    public function dump(
        $value,
        int $log_level = ilLogLevel::DEBUG
    ): void;

    public function logStack(
        int $log_level = ilLogLevel::DEBUG
    ): void;

    public function warning(
        string $message
    ): void;

    public function notice(
        string $message
    ): void;

    public function alert(
        string $message
    ): void;
}
