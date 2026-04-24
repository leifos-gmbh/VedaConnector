<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\Table;

use ilTree;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseDBFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\ImportResultInterface;
use Leifos\VedaConnector\Tables\ImportResult;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilVedaConnectorPlugin $plugin,
        protected ilTree $repository_tree,
        protected CourseDBFactoryInterface $course_db_factory
    ) {
    }

    public function importResult(
        object $class,
        string $method
    ): ImportResultInterface {
        return new ImportResult(
            $class,
            $method,
            $this->repository_tree,
            $this->plugin,
            $this->course_db_factory->handler()
        );
    }
}
