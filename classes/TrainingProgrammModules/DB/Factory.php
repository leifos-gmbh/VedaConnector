<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\BuilderInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\FactoryInterface as TrainingProgramModulesDBElementFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\FactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\HandlerInterface;
use Leifos\VedaConnector\TrainingProgramModules\DB\Element\Factory as TrainingProgramModulesDBElementFactory;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function hanlder(): HandlerInterface
    {
        return new Handler(
            $this->element(),
            $this->db,
            $this->logger_factory->handler()
        );
    }

    public function elementBuilder(): BuilderInterface
    {
        return new Builder(
            $this->element(),
            $this->hanlder(),
            $this->logger_factory->handler()
        );
    }

    public function element(): TrainingProgramModulesDBElementFactoryInterface
    {
        return new TrainingProgramModulesDBElementFactory();
    }
}
