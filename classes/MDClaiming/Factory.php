<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\MDClaiming;

use ilDBInterface;
use ilTree;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\FactoryInterface as DBFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\FactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\MDClaiming\DB\Factory as DBFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected ilDBInterface $db,
        protected ilTree $repository_tree
    ) {
    }

    public function db(): DBFactoryInterface
    {
        return new DBFactory(
            $this->logger_factory,
            $this->settings_factory,
            $this->db,
            $this->repository_tree
        );
    }
}
