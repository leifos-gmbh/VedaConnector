<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB\Element;

use Leifos\VedaConnector\I\UserStatus\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface as UserDBElementFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;

class Collection implements CollectionInterface
{
    /**
     * @var UserDBElementInterface[]
     */
    protected array $elements;
    protected int $index;

    public function __construct(
        protected UserDBElementFactoryInterface $element_factory,
        UserDBElementInterface ...$elements
    ) {
        $this->elements = $elements;
        $this->index = 0;
    }

    public function getSubCollectionOfElementsWithPendingStatus() : CollectionInterface {
        $pending_elements = [];
        foreach ($this->elements as $element) {
            if (
                $element->getCreationStatus() === Status::PENDING ||
                ($element->getCreationStatus() === Status::NONE && !$element->isImportFailure())
            ) {
                $pending_elements[] = $element;
            }
        }
        return $this->element_factory->collection(...$pending_elements);
    }

    public function count() : int
    {
        return count($this->elements);
    }

    public function current() : UserDBElementInterface
    {
        return $this->elements[$this->index];
    }

    public function key() : int
    {
        return $this->index;
    }

    public function next() : void
    {
        $this->index++;
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function valid() : bool
    {
        return isset($this->elements[$this->index]);
    }
}
