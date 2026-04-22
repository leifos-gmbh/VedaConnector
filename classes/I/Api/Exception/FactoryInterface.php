<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Exception;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
