<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus;

use ilDBInterface;
use ilTree;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\CourseStatus\Table\FactoryInterface as TableFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseStatusDBFactoryInterface;
use Leifos\VedaConnector\CourseStatus\DB\Factory as CourseStatusDBFactory;
use Leifos\VedaConnector\CourseStatus\Table\Factory as CourseStatusTableFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory,
        protected ilVedaConnectorPlugin $plugin,
        protected ilTree $repository_tree,
    ) {
    }

    public function db(): CourseStatusDBFactoryInterface
    {
        return new CourseStatusDBFactory(
            $this->db,
            $this->logger_factory
        );
    }

    public function table(): TableFactoryInterface
    {
        return new CourseStatusTableFactory(
            $this->plugin,
            $this->repository_tree,
            $this->db()
        );
    }
}
