<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB\Element;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
