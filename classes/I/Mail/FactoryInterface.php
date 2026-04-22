<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail;

use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function db(): MailDBFactoryInterface;
}
