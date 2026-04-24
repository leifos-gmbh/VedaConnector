<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB\Element;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function collection(
        HandlerInterface ...$handler
    ) : CollectionInterface;
}
