<?php

namespace Leifos\VedaConnector\UserStatus\Table;

use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\ImportResultInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected string $import_result_table_row_template_directory,
        protected UserDBFactoryInterface $user_db_factory,
        protected LangFactoryInterface $lang_factory,
    ) {
    }

    public function importResult(
        object $class,
        string $method
    ): ImportResultInterface {
        return new ImportResult(
            $class,
            $method,
            $this->import_result_table_row_template_directory,
            $this->lang_factory->handler(),
            $this->user_db_factory->handler(),
        );
    }
}
