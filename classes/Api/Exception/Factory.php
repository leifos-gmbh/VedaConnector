<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Exception;

use Leifos\VedaConnector\I\Api\Exception\FactoryInterface;
use Leifos\VedaConnector\I\Api\Exception\HandlerInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected MailDBFactoryInterface $mail_db_factory
    ) {
    }

    public function handler(): HandlerInterface {
        return new Handler(
            $this->logger,
            $this->mail_db_factory
        );
    }
}
