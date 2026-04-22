<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Logger;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
