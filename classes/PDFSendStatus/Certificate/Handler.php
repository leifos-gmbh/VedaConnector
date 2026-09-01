<?php

namespace Leifos\VedaConnector\PDFSendStatus\Certificate;

use Exception;
use ilCertificateActiveValidator;
use ilCertificatePdfAction;
use ilCertificatePdfFileNameFactory;
use ilCertificateUtilHelper;
use ilLanguage;
use ilPdfGenerator;
use ilUserCertificateRepository;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Certificate\HandlerInterface;

readonly class Handler implements HandlerInterface
{
    public function __construct(
        protected ilLanguage $lang,
        protected LoggerInterface $logger,
        protected ilCertificateActiveValidator $certificate_active_validator,
        protected ilUserCertificateRepository $user_certificate_repo,
        protected ilCertificatePdfFileNameFactory $file_name_factory
    ) {
    }

    public function getCertificateId(
        int $user_id,
        int $crs_id
    ): int {
        try {
            $certificate = $this->user_certificate_repo->fetchActiveCertificate($user_id, $crs_id);
            return $certificate->getId();
        } catch (Exception $e) {
            $this->logger->error('getCertificateId failed with message: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createCertificateFileName(
        int $certificate_id
    ): string {
        try {
            $user_certificate = $this->user_certificate_repo->fetchCertificate($certificate_id);
            $certificate = $this->user_certificate_repo->fetchActiveCertificateForPresentation($user_certificate->getUserId(), $user_certificate->getObjId());
        } catch (Exception $e) {
            $this->logger->error('createCertificateFileName failed with message: ' . $e->getMessage());
            throw $e;
        }
        return $this->file_name_factory->create($certificate);
    }

    public function createCertificateContent(
        int $certificate_id
    ): string {
        try {
            $user_certificate = $this->user_certificate_repo->fetchCertificate($certificate_id);
        } catch (Exception $e) {
            $this->logger->error('createCertificateString failed with message: ' . $e->getMessage());
            throw $e;
        }
        $pdf_action = new ilCertificatePdfAction(
            new ilPdfGenerator($this->user_certificate_repo),
            new ilCertificateUtilHelper(),
            $this->lang->txt('error_creating_certificate_pdf')
        );
        return $pdf_action->createPDF($user_certificate->getUserId(), $user_certificate->getObjId());
    }
}
