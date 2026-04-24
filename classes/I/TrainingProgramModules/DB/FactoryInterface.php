<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\FactoryInterface as TrainingProgramModulesDBElementFactoryInterface;

interface FactoryInterface
{
    public function hanlder(): HandlerInterface;

    public function elementBuilder(): BuilderInterface;

    public function element(): TrainingProgramModulesDBElementFactoryInterface;
}
