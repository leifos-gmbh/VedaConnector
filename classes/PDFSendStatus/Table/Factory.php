<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\Table;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\DI\UIServices as UIServices;
use ILIAS\HTTP\Services as HTTPServices;
use ilObjUser;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\FactoryInterface as PDFSendStatusDBFactory;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface as PDFSendStatusDBKeyFactory;
use Leifos\VedaConnector\I\PDFSendStatus\Table\DataRetrievalInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\FactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilObjUser $user,
        protected DataFactory $data_factory,
        protected UIServices $ui_services,
        protected LangFactoryInterface $lang_factory,
        protected HTTPServices $http_services,
        protected PDFSendStatusDBFactory $pdf_send_status_db_factory,
        protected PDFSendStatusDBKeyFactory $pdf_send_status_db_key_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->dataRetrieval(),
            $this->user,
            $this->data_factory,
            $this->ui_services,
            $this->lang_factory->handler(),
            $this->http_services
        );
    }

    public function dataRetrieval(): DataRetrievalInterface
    {
        return new DataRetrieval(
            $this->pdf_send_status_db_factory->handler(),
            $this->pdf_send_status_db_key_factory,
            $this->ui_services,
            $this->lang_factory->handler(),
        );
    }
}
