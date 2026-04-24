<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB\Element;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\CollectionInterface as CourseDBElementCollectionInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;

class Collection implements CourseDBElementCollectionInterface
{
    /**
     * @var CourseDBElementInterface[]
     */
    protected array $elements;
    protected int $index;

    public function __construct(
        protected CourseDBElementFactoryInterface $element_factory,
        CourseDBElementInterface ...$veda_crss
    ) {
        $this->elements = $veda_crss;
        $this->index = 0;
    }

    public function getSubCollectionOfElementsWithStatusAndType(
        Status $status,
        Type $type
    ) : CourseDBElementCollectionInterface {
        $found_elements = [];
        foreach ($this->elements as $element) {
            if (
                $element->getType() === $type &&
                $element->getCreationStatus() === $status
            ) {
                $found_elements[] = $element;
            }
        }
        return $this->element_factory->collection(...$found_elements);
    }

    public function getSubCollectionOfAsynchronusElements() : CourseDBElementCollectionInterface
    {
        $assumption_failed_seconds = 5400;
        $diff = time() - $assumption_failed_seconds;
        $found_elements = [];
        foreach ($this->elements as $element) {
            if (
                $element->getModified() < $diff &&
                $element->getCreationStatus() === Status::PENDING
            ) {
                $found_elements[] = $element;
            }
        }
        return $this->element_factory->collection(...$found_elements);
    }

    public function count() : int
    {
        return count($this->elements);
    }

    public function current() : CourseDBElementInterface
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
