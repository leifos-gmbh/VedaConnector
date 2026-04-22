<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus;

use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserStatusDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\Table\FactoryInterface as UserStatusTableFactoryInterface;

interface FactoryInterface
{
    public function db(): UserStatusDBFactoryInterface;

    public function table(): UserStatusTableFactoryInterface;
}
