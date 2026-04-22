<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB\Element;

use Iterator;
use Countable;

interface CollectionInterface extends Iterator, Countable
{
    public function getSubCollectionOfElementsWithStatusAndType(
        Status $status,
        Type $type
    ) : CollectionInterface;

    public function getSubCollectionOfAsynchronusElements() : CollectionInterface;

    public function count() : int;

    public function current() : HandlerInterface;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;
}
