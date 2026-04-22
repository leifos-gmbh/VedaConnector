<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB\Element;

use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler();
    }

    public function collection(
        HandlerInterface ...$handler
    ): CollectionInterface {
        return new Collection(
            $this,
            ...$handler
        );
    }
}
