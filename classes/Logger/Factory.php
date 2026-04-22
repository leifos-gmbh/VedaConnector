<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Logger;

use ilLogger;
use Leifos\VedaConnector\I\Logger\FactoryInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface;

class Factory implements FactoryInterface
{
    protected static HandlerInterface $handler;

    public function __construct(
        protected ilLogger $logger
    ) {
    }

    public function handler(): HandlerInterface
    {
        if (!isset(self::$handler)) {
            self::$handler = new Handler(
                \ilVedaConnectorSettings::getInstance(),
                $this->logger
            );
        }
        return self::$handler;
    }
}
