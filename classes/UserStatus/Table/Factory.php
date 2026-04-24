<?php

namespace Leifos\VedaConnector\UserStatus\Table;

use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\ImportResultInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilVedaConnectorPlugin $plugin,
        protected UserDBFactoryInterface $user_db_factory
    ) {
    }

    public function importResult(
        object $class,
        string $method
    ): ImportResultInterface {
        return new ImportResult(
            $class,
            $method,
            $this->plugin,
            $this->user_db_factory->handler(),
        );
    }
}
