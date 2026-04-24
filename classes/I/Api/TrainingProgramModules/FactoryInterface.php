<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgramModules;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
