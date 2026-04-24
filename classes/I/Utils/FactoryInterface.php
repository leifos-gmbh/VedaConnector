<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Utils;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
