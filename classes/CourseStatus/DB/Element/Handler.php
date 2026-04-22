<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB\Element;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;

class Handler implements CourseDBElementInterface
{
    protected Status $status_created;
    protected Type $type;
    protected string $oid;
    protected int $obj_id;
    protected int $switch_permanent_role;
    protected int $switch_temporary_role;
    protected int $modified;
    protected bool $document_success;

    public function __construct() {
    }

    public function getModified() : int
    {
        return $this->modified ?: time();
    }

    public function getOid() : string
    {
        return $this->oid;
    }

    public function getType() : Type
    {
        return $this->type;
    }

    public function getPermanentSwitchRole() : int
    {
        return $this->switch_permanent_role;
    }

    public function getTemporarySwitchRole() : int
    {
        return $this->switch_temporary_role;
    }

    public function getCreationStatus() : Status
    {
        return $this->status_created;
    }

    public function getObjId() : int
    {
        return $this->obj_id;
    }

    public function getDocumentSuccess() : bool
    {
        return $this->document_success;
    }

    public function toString() : string
    {
        return "Course with parameters: "
            . "\nOID: " . $this->oid
            . "\nObjID: " . $this->obj_id
            . "\nPRole: " . $this->switch_permanent_role
            . "\nTRole: " . $this->switch_temporary_role
            . "\nStatusCreated: " . $this->status_created->name
            . "\nModified: " . $this->modified
            . "\nType: " . $this->type->name;
    }

    public function withObjId(
        int $obj_id
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->obj_id = $obj_id;
        return $clone;
    }

    public function withDocumentSucceeded(
        bool $value
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->document_success = $value;
        return $clone;
    }

    public function withModified(
        int $modified
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->modified = $modified;
        return $clone;
    }

    public function withOid(
        string $oid
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->oid = $oid;
        return $clone;
    }

    public function withPermanentSwitchRole(
        int $role
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->switch_permanent_role = $role;
        return $clone;
    }

    public function withType(
        Type $type
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function withCreationStatus(
        Status $status
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->status_created = $status;
        return $clone;
    }

    public function withTemporarySwitchRole(
        int $role
    ): CourseDBElementInterface {
        $clone = clone $this;
        $clone->switch_temporary_role = $role;
        return $clone;
    }
}
