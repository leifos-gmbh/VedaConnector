<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\MDClaiming;

use Leifos\VedaConnector\I\MDClaiming\DB\FactoryInterface as DBFactoryInterface;

interface FactoryInterface
{
    public function db(): DBFactoryInterface;
}
