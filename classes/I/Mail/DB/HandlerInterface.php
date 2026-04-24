<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB;

use Leifos\VedaConnector\I\Mail\DB\Element\CollectionInterface as MailDBElementCollectionInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface as MailDBElementInterface;

interface HandlerInterface
{
    public const TABLE_NAME = 'cron_crnhk_vedaimp_ml';

    public function lookupAll() : MailDBElementCollectionInterface;

    public function write(
        MailDBElementInterface $element
    ) : void;

    public function delete(
        MailDBElementInterface $element
    ) : void;

    public function deleteAll() : void;
}
