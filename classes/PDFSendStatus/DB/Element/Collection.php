<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Element;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface;

class Collection implements CollectionInterface
{
    /**
     * @var HandlerInterface[]
     */
    protected array $elements;
    protected int $index;

    public function __construct(
        HandlerInterface ...$elements
    ) {
        $this->elements = $elements;
        $this->index = 0;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function current(): HandlerInterface
    {
        return $this->elements[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function valid(): bool
    {
        return isset($this->elements[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return count($this->elements);
    }
}
