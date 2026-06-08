<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus;

use ilDBInterface;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\FactoryInterface as UserTableFactoryInterface;
use Leifos\VedaConnector\UserStatus\DB\Factory as UserDBFactory;
use Leifos\VedaConnector\UserStatus\Table\Factory as UserTableFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected string $import_result_table_row_template_directory,
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory,
        protected LangFactoryInterface $lang_factory
    ) {
    }

    public function db(): UserDBFactoryInterface
    {
        return new UserDBFactory(
            $this->db,
            $this->logger_factory
        );
    }

    public function table(): UserTableFactoryInterface
    {
        return new UserTableFactory(
            $this->import_result_table_row_template_directory,
            $this->db(),
            $this->lang_factory
        );
    }
}
