<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus;

use ilDBInterface;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\FactoryInterface as UserTableFactoryInterface;
use Leifos\VedaConnector\UserStatus\DB\Factory as UserDBFactory;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\UserStatus\Table\Factory as UserTableFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory,
        protected ilVedaConnectorPlugin $plugin
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
            $this->plugin,
            $this->db()
        );
    }
}
