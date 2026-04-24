<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Utils;

use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Utils\FactoryInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->logger_factory->handler()
        );
    }
}
