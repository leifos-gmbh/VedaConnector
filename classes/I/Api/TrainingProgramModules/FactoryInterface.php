<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgramModules;

use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use GuzzleHttp\Client as GClient;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
