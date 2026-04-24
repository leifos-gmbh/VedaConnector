<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface as ElementFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface;
use Leifos\VedaConnector\UserStatus\DB\Element\Builder;
use Leifos\VedaConnector\UserStatus\DB\Element\Factory as ElementFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function element(): ElementFactoryInterface
    {
        return new ElementFactory(
            $this->logger_factory
        );
    }

    public function elementBuilder(): Builder
    {
        return new Builder(
            $this->element(),
            $this->handler(),
            $this->logger_factory->handler()
        );
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->element(),
            $this->db,
            $this->logger_factory->handler()
        );
    }
}
