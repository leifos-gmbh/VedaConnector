<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\FactoryInterface as ElementFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface as KeyFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function key(): KeyFactoryInterface;

    public function element(): ElementFactoryInterface;
}
