<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules;

use ilDBInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\FactoryInterface as TrainingProgramModulesDBFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\FactoryInterface;
use Leifos\VedaConnector\TrainingProgramModules\DB\Factory as TrainingProgramModulesDBFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function db(): TrainingProgramModulesDBFactoryInterface
    {
        return new TrainingProgramModulesDBFactory(
            $this->db,
            $this->logger_factory
        );
    }
}
