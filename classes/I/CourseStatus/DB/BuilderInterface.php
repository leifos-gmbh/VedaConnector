<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;

interface BuilderInterface
{
    public function withOId(
        string $oid,
        bool $load_from_db = true
    ) : BuilderInterface;

    public function withObjId(
        int $obj_id
    ) : BuilderInterface;

    public function withSwitchPermanentRole(
        int $role_id
    ) : BuilderInterface;

    public function withSwithTemporaryRole(
        int $role_id
    ) : BuilderInterface;

    public function withStatusCreated(
        Status $status
    ) : BuilderInterface;

    public function withModified(
        int $modified
    ) : BuilderInterface;

    public function withType(
        Type $type
    ) : BuilderInterface;

    public function withDocumentSuccess(
        bool $value
    ) : BuilderInterface;

    public function store() : void;

    public function get() : CourseDBElementInterface;
}
