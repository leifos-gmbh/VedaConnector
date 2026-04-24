<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB;

use ilDBConstants;
use ilDBInterface;
use ilObjCourse;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\CollectionInterface as CourseDBElementCollectionInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface as CourseDBElementInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface as CourseDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Handler implements CourseDBInterface
{
    public function __construct(
        protected CourseDBElementFactoryInterface $element_factory,
        protected ilDBInterface $db,
        protected LoggerInterface $logger
    ) {
    }

    protected function refIDtoOID(
        int $ref_id
    ): ?string {
        $import_id = ilObjCourse::_lookupImportId($ref_id);
        if (!$import_id) {
            $this->logger->debug('No veda user for event found.');
            return null;
        }
        return $import_id;
    }

    public function update(
        CourseDBElementInterface $element
    ): void {
        $this->logger->debug('Updating course with oid: ' . $element->getOid());
        $query = 'INSERT INTO ' . CourseDBInterface::TABLE_NAME
            . ' (oid, obj_id, switchp, switcht, status_created, modified, type, document_success) '
            . ' VALUES ('
            . $this->db->quote($element->getOid(), ilDBConstants::T_TEXT) . ', '
            . $this->db->quote($element->getObjId(), ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote($element->getPermanentSwitchRole(), ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote($element->getTemporarySwitchRole(), ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote($element->getCreationStatus()->value, ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote($element->getModified(), ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote($element->getType()->value, ilDBConstants::T_INTEGER) . ', '
            . $this->db->quote((int) $element->getDocumentSuccess(), ilDBConstants::T_INTEGER)
            . ') ON DUPLICATE KEY UPDATE '
            . 'oid=VALUES(oid), '
            . 'obj_id=VALUES(obj_id), '
            . 'switchp=VALUES(switchp), '
            . 'switcht=VALUES(switcht), '
            . 'status_created=VALUES(status_created), '
            . 'modified=VALUES(modified), '
            . 'type=VALUES(type), '
            . 'document_success=VALUES(document_success)';
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function deleteByOId(
        string $oid
    ): void {
        $this->logger->debug('Deleting course with oid: ' . $oid);
        $query = 'delete from ' . self::TABLE_NAME . ' '
            . 'where oid = ' . $this->db->quote($oid, ilDBConstants::T_TEXT);
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function lookupByOId(
        string $oid
    ): ?CourseDBElementInterface {
        $this->logger->debug('Looking up course by oid: ' . $oid);
        $query = 'select * from ' . self::TABLE_NAME . ' ' .
            'where oid = ' . $this->db->quote($oid, ilDBConstants::T_TEXT);
        $this->logger->debug($query);
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $this->element_factory->handler()
                ->withOid($row->oid)
                ->withObjId((int) $row->obj_id)
                ->withModified((int) $row->modified)
                ->withType(Type::from((int) $row->type))
                ->withPermanentSwitchRole((int) $row->switchp)
                ->withTemporarySwitchRole((int) $row->switcht)
                ->withCreationStatus(Status::from((int) $row->status_created))
                ->withDocumentSucceeded( (bool) $row->document_success);
        }
        return null;
    }

    public function lookupById(
        int $ref_id
    ): ?CourseDBElementInterface {
        $this->logger->debug('Lookup up course by ref_id: ' . $ref_id);
        $oid = $this->refIDtoOID($ref_id);
        if (is_null($oid)) {
            return null;
        }
        return $this->lookupByOId($oid);
    }

    public function lookupAll(): CourseDBElementCollectionInterface
    {
        $this->logger->debug('Looking up all courses.');
        $query = 'select * from ' . self::TABLE_NAME;
        $this->logger->debug($query);
        $res = $this->db->query($query);
        $elements = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $elements[] = $this->element_factory->handler()
                ->withOid($row->oid)
                ->withObjId((int) $row->obj_id)
                ->withModified((int) $row->modified)
                ->withType(Type::from((int) $row->type))
                ->withPermanentSwitchRole((int) $row->switchp)
                ->withTemporarySwitchRole((int) $row->switcht)
                ->withCreationStatus(Status::from((int) $row->status_created))
                ->withDocumentSucceeded( (bool) $row->document_success);
        }
        $this->logger->debug('Found ' . count($elements));
        return $this->element_factory->collection(...$elements);
    }

    public function lookupByStatusAndType(
        Status $status,
        Type $type
    ): CourseDBElementCollectionInterface {
        $this->logger->debug('Looking up all courses.');
        $query = 'select * from ' . self::TABLE_NAME . ' where'
            . ' type = ' . $this->db->quote($type, ilDBConstants::T_INTEGER)
            . ' and status_created = ' . $this->db->quote($status, ilDBConstants::T_INTEGER);
        $this->logger->debug($query);
        $res = $this->db->query($query);
        $elements = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $elements[] = $this->element_factory->handler()
                ->withOid($row->oid)
                ->withObjId((int) $row->obj_id)
                ->withModified((int) $row->modified)
                ->withType(Type::from((int) $row->type))
                ->withPermanentSwitchRole((int) $row->switchp)
                ->withTemporarySwitchRole((int) $row->switcht)
                ->withCreationStatus(Status::from((int) $row->status_created))
                ->withDocumentSucceeded((bool) $row->document_success);
        }
        $this->logger->debug('Found ' . count($elements));
        return $this->element_factory->collection(...$elements);
    }
}
