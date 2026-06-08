<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus;

use ilDBInterface;
use ilTree;
use Leifos\VedaConnector\CourseStatus\DB\Factory as CourseStatusDBFactory;
use Leifos\VedaConnector\CourseStatus\Table\Factory as CourseStatusTableFactory;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseStatusDBFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\FactoryInterface as TableFactoryInterface;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected string $table_row_template_directory,
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory,
        protected ilTree $repository_tree,
        protected LangFactoryInterface $lang_factory
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
            $this->table_row_template_directory,
            $this->repository_tree,
            $this->db(),
            $this->lang_factory
        );
    }
}
