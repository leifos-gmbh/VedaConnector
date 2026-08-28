<?php

namespace Leifos\VedaConnector\I\PDFSendStatus\Certificate;

interface HandlerInterface
{
    public function createCertificateFileName(
        int $certificate_id
    ): string;

    public function createCertificateContent(
        int $certificate_id
    ): string;
}
