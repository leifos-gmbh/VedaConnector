<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB;

use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface as ElementFactoryInterface;
use Leifos\VedaConnector\UserStatus\DB\Element\Builder;

interface FactoryInterface
{
    public function element(): ElementFactoryInterface;

    public function elementBuilder(): Builder;

    public function handler(): HandlerInterface;
}
