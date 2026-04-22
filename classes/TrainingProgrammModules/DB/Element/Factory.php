<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules\DB\Element;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\FactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\Type;

class Factory implements FactoryInterface
{
    public function handler(): HandlerInterface {
        return new Handler();
    }
}
