<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\MDClaiming\DB;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
