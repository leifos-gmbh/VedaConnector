<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\MDClaiming;

use Leifos\VedaConnector\I\MDClaiming\FactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\HandlerInterface;

class Factory implements FactoryInterface
{
    public function handler(): HandlerInterface
    {
        // TODO: Implement handler() method.
    }
}
