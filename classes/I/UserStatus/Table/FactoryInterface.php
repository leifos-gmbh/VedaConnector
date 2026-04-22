<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\Table;

interface FactoryInterface
{
    public function importResult(
        object $class,
        string $method
    ): ImportResultInterface;
}
