<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Key;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface as ElementInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function mapping(): MappingInterface;

    public function createKeyForElement(
        ElementInterface $element
    ): HandlerInterface;

    public function createKeyForAllElements(): HandlerInterface;
}
