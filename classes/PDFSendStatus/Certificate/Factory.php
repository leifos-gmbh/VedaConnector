<?php

namespace Leifos\VedaConnector\PDFSendStatus\Certificate;

use ilCertificateActiveValidator;
use ilCertificatePdfFileNameFactory;
use ilLanguage;
use ilUserCertificateRepository;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Certificate\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Certificate\HandlerInterface;

readonly class Factory implements FactoryInterface
{
    public function __construct(
        protected ilLanguage $lang,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->lang,
            $this->logger_factory->handler(),
            new ilCertificateActiveValidator(),
            new ilUserCertificateRepository(),
            new ilCertificatePdfFileNameFactory($this->lang)
        );
    }
}
