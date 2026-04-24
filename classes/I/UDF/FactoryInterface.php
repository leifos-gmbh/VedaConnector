<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UDF;

use Leifos\VedaConnector\I\UDF\DB\FactoryInterface as DBFactoryInterface;

interface FactoryInterface
{
    public function db(): DBFactoryInterface;
}
