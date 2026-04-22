<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB;

use Leifos\VedaConnector\I\CourseStatus\DB\BuilderInterface as CourseDBBuilderInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface as CourseDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;

class Builder implements CourseDBBuilderInterface
{
    protected CourseDBElementInterface $element;

    public function __construct(
        protected CourseDBElementFactoryInterface $course_element_factory,
        protected CourseDBInterface $course_db,
        protected LoggerInterface $logger
    ) {
        $this->element = $this->course_element_factory->handler();
    }

    public function withOId(
        string $oid,
        bool $load_from_db = true
    ): CourseDBBuilderInterface {
        $new_builder = clone $this;
        $this->logger->debug($load_from_db
            ? 'Looking for existing veda course with oid: ' . $oid
            : 'Skip looking for an existing veda cours with oid: ' . $oid);
        $existing_crs = $load_from_db
            ? $this->course_db->lookupByOId($oid)
            : null;
        if (is_null($existing_crs)) {
            $this->logger->debug('Course with id does not exist, or data base lookup skipped.');
            $new_builder->element = $this->element
                ->withOid($oid);
        }
        if (!is_null($existing_crs)) {
            $this->logger->debug('Course with id found');
            $new_builder->element = $existing_crs;
        }
        return $new_builder;
    }

    public function withObjId(
        int $obj_id
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withObjId($obj_id);
        return $new_builder;
    }

    public function withSwitchPermanentRole(
        int $role_id
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withPermanentSwitchRole($role_id);
        return $new_builder;
    }

    public function withSwithTemporaryRole(
        int $role_id
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withTemporarySwitchRole($role_id);;
        return $new_builder;
    }

    public function withStatusCreated(
        Status $status
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withCreationStatus($status);
        return $new_builder;
    }

    public function withModified(
        int $modified
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withModified($modified);
        return $new_builder;
    }

    public function withType(
        Type $type
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withType($type);
        return $new_builder;
    }

    public function withDocumentSuccess(
        bool $value
    ) : CourseDBBuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element
            ->withDocumentSucceeded($value);
        return $new_builder;
    }

    public function store() : void
    {
        $this->logger->debug('Updating veda course');
        if ($this->element->getOid() === "") {
            $this->logger->debug('Cannot update veda course with null id');
            return;
        }
        $this->course_db->update($this->element);
    }

    public function get() : CourseDBElementInterface
    {
        return $this->element;
    }
}
