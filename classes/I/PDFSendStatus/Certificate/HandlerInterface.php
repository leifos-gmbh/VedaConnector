<?php

namespace Leifos\VedaConnector\I\PDFSendStatus\Certificate;

interface HandlerInterface
{
    public function getCertificateId(
        int $user_id,
        int $crs_id
    ): int;

    public function createCertificateFileName(
        int $certificate_id
    ): string;

    public function createCertificateContent(
        int $certificate_id
    ): string;
}
