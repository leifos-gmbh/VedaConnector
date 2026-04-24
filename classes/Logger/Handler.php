<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Logger;

use ilLineFormatter;
use ilLogger;
use ilLogLevel;
use Leifos\VedaConnector\I\Logger\HandlerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

class Handler implements HandlerInterface
{
    public function __construct(
        protected SettingsInterface $settings,
        protected ilLogger $logger
    ) {
        $this->logger->debug('Set log level to: ' . $settings->readAsInt(SettingsName::LOGLEVEL));
        if (
            $settings->readAsInt(SettingsName::LOGLEVEL) != ilLogLevel::OFF &&
            strcmp($settings->read(SettingsName::LOGFILE), '') != 0
        ) {
            $stream_handler = new StreamHandler(
                $settings->read(SettingsName::LOGFILE),
                $settings->readAsInt(SettingsName::LOGLEVEL),
                true
            );
            $default_format = "[%suid%] [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $line_formatter = new ilLineFormatter($default_format, 'Y-m-d H:i:s.u', true, true);
            $stream_handler->setFormatter($line_formatter);
            $this->logger->getLogger()->pushHandler($stream_handler);
        }
        foreach ($this->logger->getLogger()->getHandlers() as $handler) {
            if (!$handler instanceof NullHandler) {
                $handler->setLevel($settings->readAsInt(SettingsName::LOGLEVEL));
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
