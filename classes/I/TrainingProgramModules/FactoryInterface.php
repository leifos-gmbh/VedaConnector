<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\FactoryInterface as TrainingProgramModulesDBFactoryInterface;

interface FactoryInterface
{
    public function db(): TrainingProgramModulesDBFactoryInterface;
}
