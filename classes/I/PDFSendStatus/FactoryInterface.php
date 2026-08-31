<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus;

use Leifos\VedaConnector\I\PDFSendStatus\Certificate\FactoryInterface as CertificateFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\FactoryInterface as DBFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\FactoryInterface as TableFactoryInterface;

interface FactoryInterface
{
    public function db(): DBFactoryInterface;

    public function table(): TableFactoryInterface;

    public function certificate(): CertificateFactoryInterface;
}
