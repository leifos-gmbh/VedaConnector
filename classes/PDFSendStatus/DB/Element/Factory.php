<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Element;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface;

class Factory implements FactoryInterface
{

    public function handler(): HandlerInterface
    {
        return new Handler();
    }

    public function collection(
        HandlerInterface ...$elements
    ): CollectionInterface {
        return new Collection(...$elements);
    }
}
