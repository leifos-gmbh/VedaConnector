<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Key;

use ilDBInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface as ElementInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\HandlerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\MappingInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->db
        );
    }

    public function mapping(): MappingInterface
    {
        return new Mapping();
    }

    public function createKeyForElement(
        ElementInterface $element
    ): HandlerInterface {
        return $this->handler()->withDBSequenceIds($element->getDBSequenceId());
    }

    public function createKeyForAllElements(): HandlerInterface
    {
        return $this->handler();
    }
}
