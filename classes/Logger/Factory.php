<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Logger;

use ilLogger;
use Leifos\VedaConnector\I\Logger\FactoryInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;

class Factory implements FactoryInterface
{
    protected static HandlerInterface $handler;

    public function __construct(
        protected ilLogger $logger,
        protected SettingsFactoryInterface $settings_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        if (!isset(self::$handler)) {
            self::$handler = new Handler(
                $this->settings_factory->handler(),
                $this->logger
            );
        }
        return self::$handler;
    }
}
