<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB\Element;

use Countable;
use DateTimeImmutable;
use Iterator;

interface CollectionInterface extends Iterator, Countable
{
    public function getSubCollectionByType(
        Type $type
    ) : CollectionInterface;

    public function getSubCollectionByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ) : CollectionInterface;

    public function next() : void;

    public function count() : int;

    public function current() : HandlerInterface;

    public function key() : int;

    public function rewind() : void;

    public function valid() : bool;
}
