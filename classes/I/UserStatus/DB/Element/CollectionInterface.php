<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB\Element;

use Countable;
use Iterator;

interface CollectionInterface extends Iterator, Countable
{
    public function getSubCollectionOfElementsWithPendingStatus() : CollectionInterface;

    public function count() : int;

    public function current() : HandlerInterface;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;
}
