<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\CollectionInterface as CourseDBElementCollectionInterface;

interface HandlerInterface
{
    public const TABLE_NAME = 'cron_crnhk_vedaimp_crs';

    public function update(
        CourseDBElementInterface $element
    ) : void;

    public function deleteByOId(
        string $oid
    ) : void;

    public function lookupByOId(
        string $oid
    ) : ?CourseDBElementInterface;

    public function lookupById(
        int $ref_id
    ) : ?CourseDBElementInterface;

    public function lookupAll() : CourseDBElementCollectionInterface;

    public function lookupByStatusAndType(
        Status $status,
        Type $type
    ) : CourseDBElementCollectionInterface;
}
