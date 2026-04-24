<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingCourse;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
