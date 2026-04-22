<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules\DB;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\BuilderInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\HandlerInterface as TrainingProgramModulesDBInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface as TrainingProgramModulesDBElementInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\FactoryInterface as TrainingProgramModulesDBElementFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\Type;

class Builder implements BuilderInterface
{
    protected TrainingProgramModulesDBElementInterface $element;

    public function __construct(
        protected TrainingProgramModulesDBElementFactoryInterface $element_factory,
        protected TrainingProgramModulesDBInterface $traing_programm_modules_db,
        protected LoggerInterface $logger
    ) {
        $this->element = $this->element_factory->handler();
    }

    public function withOId(
        string $oid,
        bool $load_from_db = true
    ) : BuilderInterface {
        $new_builder = clone $this;
        $this->logger->debug($load_from_db
            ? 'Looking for existing veda course with oid: ' . $oid
            : 'Skip looking for an existing veda cours with oid: ' . $oid);
        $existing_crs = $load_from_db
            ? $this->traing_programm_modules_db->lookupByOId($oid)
            : null;
        if (is_null($existing_crs)) {
            $this->logger->debug('Course with id does not exist, or data base lookup skipped.');
            $new_builder->element = $this->element
                ->withOId($oid);
        }
        if (!is_null($existing_crs)) {
            $this->logger->debug('Course with id found');
            $new_builder->element = $existing_crs;
        }
        return $new_builder;
    }

    public function withType(
        Type $type
    ) : BuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element->withType($type);
        return $new_builder;
    }

    public function get() : TrainingProgramModulesDBElementInterface
    {
        return $this->element;
    }

    public function store() : void
    {
        $this->traing_programm_modules_db->update($this->element);
    }
}
