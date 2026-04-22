<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface as EntryFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\HandlerInterface;
use Leifos\VedaConnector\Mail\DB\Element\Factory as ElementFactory;
use Leifos\VedaConnector\I\Mail\DB\BuilderInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->element(),
            $this->db,
            $this->logger_factory->handler()
        );
    }

    public function element(): EntryFactoryInterface
    {
        return new ElementFactory(
            $this->logger_factory
        );
    }

    public function elementBuilder(): BuilderInterface
    {
        return new Builder(
            $this->handler(),
            $this->logger_factory->handler(),
            $this->element()
        );
    }
}
