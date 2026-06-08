<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Lang;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
