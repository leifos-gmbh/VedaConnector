<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\MDClaiming;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
