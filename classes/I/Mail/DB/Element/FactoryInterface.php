<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB\Element;

interface FactoryInterface
{
    public function collection(
        HandlerInterface ...$handler
    ): CollectionInterface;

    public function handler(
        int $id
    ): HandlerInterface;
}
