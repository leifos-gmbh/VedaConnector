<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Logger;

use ilLineFormatter;
use ilLogger;
use ilLogLevel;
use ilVedaConnectorSettings;
use Leifos\VedaConnector\I\Logger\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;

class Handler implements HandlerInterface
{
    public function __construct(
        protected ilVedaConnectorSettings $settings,
        protected ilLogger $logger
    ) {
        $this->logger->debug('Set log level to: ' . $settings->getLogLevel());
        if (
            $settings->getLogLevel() != ilLogLevel::OFF &&
            $settings->getLogFile() != ''
        ) {
            $stream_handler = new StreamHandler(
                $settings->getLogFile(),
                $settings->getLogLevel(),
                true
            );
            $default_format = "[%suid%] [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $line_formatter = new ilLineFormatter($default_format, 'Y-m-d H:i:s.u', true, true);
            $stream_handler->setFormatter($line_formatter);
            $this->logger->getLogger()->pushHandler($stream_handler);
        }
        foreach ($this->logger->getLogger()->getHandlers() as $handler) {
            if (!$handler instanceof NullHandler) {
                $handler->setLevel($settings->getLogLevel());
            }
        }
    }

    public function debug(
        string $message
    ): void {
        $this->logger->debug($message);
    }

    public function info(
        string $message
    ): void {
        $this->logger->info($message);
    }

    public function error(
        string $message
    ): void {
        $this->logger->error($message);
    }

    public function dump(
        $value,
        int $log_level = ilLogLevel::DEBUG
    ): void {
        $this->logger->dump($value, $log_level);;
    }

    public function logStack(
        int $log_level = ilLogLevel::DEBUG
    ): void {
        $this->logger->logStack($log_level);
    }

    public function warning(
        string $message
    ): void {
        $this->logger->warning($message);
    }

    public function notice(
        string $message
    ): void {
        $this->logger->notice($message);
    }

    public function alert(
        string $message
    ): void {
        $this->logger->alert($message);
    }
}
