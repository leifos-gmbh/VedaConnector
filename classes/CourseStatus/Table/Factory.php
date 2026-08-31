<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\Table;

use ilTree;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseDBFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\ImportResultInterface;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\Tables\ImportResult;

class Factory implements FactoryInterface
{
    public function __construct(
        protected string $table_row_template_directory,
        protected ilTree $repository_tree,
        protected CourseDBFactoryInterface $course_db_factory,
        protected LangFactoryInterface $lang_factory
    ) {
    }

    public function importResult(
        object $class,
        string $method
    ): ImportResultInterface {
        return new ImportResult(
            $class,
            $method,
            $this->table_row_template_directory,
            $this->repository_tree,
            $this->lang_factory->handler(),
            $this->course_db_factory->handler()
        );
    }
}
