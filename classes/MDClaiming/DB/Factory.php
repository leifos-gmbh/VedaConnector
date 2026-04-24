<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\MDClaiming\DB;

use ilDBInterface;
use ilTree;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\FactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\HandlerInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected ilDBInterface $db,
        protected ilTree $repository_tree
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->logger_factory->handler(),
            $this->settings_factory->handler(),
            $this->db,
            $this->repository_tree
        );
    }
}
