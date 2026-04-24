<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail;

use ilDBInterface;
use ilMailMimeSenderFactory;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;
use Leifos\VedaConnector\I\Mail\FactoryInterface;
use Leifos\VedaConnector\I\Mail\HandlerInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\Mail\DB\Factory as MailDBFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilMailMimeSenderFactory $sender_factory,
        protected LoggerFactoryInterface $logger_factory,
        protected ilDBInterface $db,
        protected SettingsFactoryInterface $settings_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->sender_factory,
            $this->db()->handler(),
            $this->logger_factory->handler(),
            $this->settings_factory->handler()
        );
    }

    public function db(): MailDBFactoryInterface
    {
        return new MailDBFactory(
            $this->db,
            $this->logger_factory
        );
    }
}
