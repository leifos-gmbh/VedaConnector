<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingCourse;

use GuzzleHttp\Client as GClient;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
