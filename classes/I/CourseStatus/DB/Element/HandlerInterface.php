<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB\Element;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;

interface HandlerInterface
{
    public function getModified() : int;

    public function getOid() : string;

    public function getType() : Type;

    public function getPermanentSwitchRole() : int;

    public function getTemporarySwitchRole() : int;

    public function getCreationStatus() : Status;

    public function getDocumentSuccess() : bool;

    public function getObjId() : int;

    public function withObjId(
        int $obj_id
    ): HandlerInterface;

    public function withDocumentSucceeded(
        bool $value
    ): HandlerInterface;

    public function withModified(
        int $modified
    ): HandlerInterface;

    public function withOid(
        string $oid
    ): HandlerInterface;

    public function withPermanentSwitchRole(
        int $role
    ): HandlerInterface;

    public function withType(
        Type $type
    ): HandlerInterface;

    public function withCreationStatus(
        Status $status
    ): HandlerInterface;

    public function withTemporarySwitchRole(
        int $role
    ): HandlerInterface;

    public function toString(): string;
}
