<?php

namespace Leifos\VedaConnector\I\PDFSendStatus\Certificate;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
