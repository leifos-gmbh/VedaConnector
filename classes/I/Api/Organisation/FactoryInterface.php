<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Organisation;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
