<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform;

use GuzzleHttp\Client as GClient;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\FactoryInterface as CollectionsFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function collections(): CollectionsFactoryInterface;
}
