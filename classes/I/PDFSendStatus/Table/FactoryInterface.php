<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\Table;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function dataRetrieval(): DataRetrievalInterface;
}
