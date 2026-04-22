<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB\Element;

use DateTimeImmutable;
use Leifos\VedaConnector\I\Mail\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface as EntryFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface as EntryHandlerInterface;
use Leifos\VedaConnector\I\Mail\DB\Type;

class Collection implements CollectionInterface
{
    /**
     * @var EntryHandlerInterface[]
     */
    protected array $entries;
    protected int $index;

    public function __construct(
        protected EntryFactoryInterface $entry_factory,
        EntryHandlerInterface ...$entries
    ) {
        $this->entries = $entries;
        $this->index = 0;
    }

    public function current() : EntryHandlerInterface
    {
        return $this->entries[$this->index];
    }

    public function next() : void
    {
        $this->index++;
    }

    public function key() : int
    {
        return $this->index;
    }

    public function valid() : bool
    {
        return 0 <= $this->index && $this->index < count($this->entries);
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function getSubCollectionByType(
        Type $type
    ) : CollectionInterface {
        $entries = [];
        foreach ($this->entries as $entry) {
            if ($entry->getType() === $type) {
                $entries[] = $entry;
            }
        }
        return $this->entry_factory->collection(...$entries);
    }

    public function getSubCollectionByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ) : CollectionInterface {
        if ($from > $to) {
            $tmp = $from;
            $from = $to;
            $to = $tmp;
        }
        $entries = [];
        foreach ($this->entries as $entry) {
            if ($from <= $entry->getLastModified() && $entry->getLastModified() <= $to) {
                $entries[] = $entry;
            }
        }
        return $this->entry_factory->collection(...$entries);
    }

    public function count() : int
    {
        return count($this->entries);
    }
}
