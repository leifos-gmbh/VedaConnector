<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\FactoryInterface as ElementFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\HandlerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface as KeyFactoryInterface;
use Leifos\VedaConnector\PDFSendStatus\DB\Element\Factory as ElementFactory;
use Leifos\VedaConnector\PDFSendStatus\DB\Key\Factory as KeyFactory;
use Leifos\VedaConnector\PDFSendStatus\DB\Handler as PDFSendStatusDB;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new PDFSendStatusDB(
            $this->db,
            $this->key(),
            $this->element(),
            $this->logger_factory->handler()
        );
    }

    public function key(): KeyFactoryInterface
    {
        return new KeyFactory(
            $this->db
        );
    }

    public function element(): ElementFactoryInterface
    {
        return new ElementFactory();
    }
}
