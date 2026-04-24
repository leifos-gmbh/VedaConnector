<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UDF\DB;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
