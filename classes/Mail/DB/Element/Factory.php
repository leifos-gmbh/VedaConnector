<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB\Element;

use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface;
use Leifos\VedaConnector\I\Mail\DB\HandlerInterface as MailDBInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function collection(
        HandlerInterface ...$handler
    ): CollectionInterface {
        return new Collection(...$handler);
    }

    public function handler(
        int $id
    ): HandlerInterface {
        return new Handler($id);
    }
}
