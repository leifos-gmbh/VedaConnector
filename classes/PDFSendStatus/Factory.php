<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus;

use ilDBInterface;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\DI\UIServices as UIServices;
use ILIAS\HTTP\Services as HTTPServices;
use ilLanguage;
use ilObjUser;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Certificate\FactoryInterface as CertificateFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\FactoryInterface as DBFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\FactoryInterface as TableFactoryInterface;
use Leifos\VedaConnector\PDFSendStatus\Certificate\Factory as CertificateFactory;
use Leifos\VedaConnector\PDFSendStatus\DB\Factory as DBFactory;
use Leifos\VedaConnector\PDFSendStatus\Table\Factory as PDFSendStatusTableFactory;

readonly class Factory implements FactoryInterface
{
    public function __construct(
        protected ilLanguage $lang,
        protected ilDBInterface $db,
        protected ilObjUser $user,
        protected DataFactory $data_factory,
        protected UIServices $ui_services,
        protected LangFactoryInterface $lang_factory,
        protected HTTPServices $http_services,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function db(): DBFactoryInterface
    {
        return new DBFactory(
            $this->db,
            $this->logger_factory
        );
    }

    public function table(): TableFactoryInterface
    {
        return new PDFSendStatusTableFactory(
            $this->user,
            $this->data_factory,
            $this->ui_services,
            $this->lang_factory,
            $this->http_services,
            $this->db(),
            $this->db()->key()
        );
    }

    public function certificate(): CertificateFactoryInterface
    {
        return new CertificateFactory(
            $this->lang,
            $this->logger_factory
        );
    }
}
