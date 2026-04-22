<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB;

use ilDBConstants;
use ilDBInterface;
use ilObjUser;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface as UserDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface as UserDBElementFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\CollectionInterface as UserDBCollectionInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;

class Handler implements UserDBInterface
{
    /**
     * @var string
     */
    private const TABLE_NAME = 'cron_crnhk_vedaimp_us';

    public function __construct(
        protected UserDBElementFactoryInterface $element_factory,
        protected ilDBInterface $db,
        protected LoggerInterface $logger
    ) {
    }

    protected function refIDtoOID(int $ref_id) : ?string
    {
        $import_id = ilObjUser::_lookupImportId($ref_id);
        if (!$import_id) {
            $this->logger->debug('No veda user for event found.');
            return null;
        }
        return $import_id;
    }

    public function update(
        UserDBElementInterface $element
    ) : void {
        $this->logger->debug('Updating user with oid: ' . $element->getOid());
        $query = 'INSERT INTO ' . self::TABLE_NAME . ' (oid, login, status_pwd, status_created, import_failure)'
            . ' VALUES ('
            . $this->db->quote($element->getOid(), 'text') . ', '
            . $this->db->quote($element->getLogin(), 'text') . ', '
            . $this->db->quote($element->getPasswordStatus()->value, 'integer') . ', '
            . $this->db->quote($element->getCreationStatus()->value, 'integer') . ', '
            . $this->db->quote($element->isImportFailure(), 'integer')
            . ') ON DUPLICATE KEY UPDATE '
            . 'oid=VALUES(oid), '
            . 'login=VALUES(login), '
            . 'status_pwd=VALUES(status_pwd), '
            . 'status_created=VALUES(status_created), '
            . 'import_failure=VALUES(import_failure)';
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function deleteByOId(
        string $oid
    ) : void {
        $this->logger->debug('Deleting user by oid: ' . $oid);
        $query = 'delete from ' . self::TABLE_NAME . ' ' .
            'where oid = ' . $this->db->quote($oid, 'text');
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function deleteById(
        int $usr_id
    ) : void {
        $this->logger->debug('Deleting user by id: ' . $usr_id);
        $import_id = $this->refIDtoOID($usr_id);
        if (!is_null($import_id)) {
            $this->deleteByOId($import_id);
        }
    }

    public function lookupByOId(
        string $oid
    ) : ?UserDBElementInterface {
        $this->logger->debug('Lookup user by oid: ' . $oid);
        $query = 'select * from ' . self::TABLE_NAME . ' ' .
            'where oid = ' . $this->db->quote($oid, 'text');
        $this->logger->debug($query);
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->element_factory->handler()
                ->withOid($row->oid)
                ->withLogin($row->login)
                ->withPasswordStatus(Status::from((int) $row->status_pwd))
                ->withCreationStatus(Status::from((int) $row->status_created))
                ->withImportStatusFailed((bool) ((int) $row->import_failure));
        }
        return null;
    }

    public function lookupById(
        int $ref_id
    ) : ?UserDBElementInterface {
        $this->logger->debug('Lookup user by ref_id: ' . $ref_id);
        $oid = $this->refIDtoOID($ref_id);
        return is_null($oid) ? null : $this->lookupByOId($oid);
    }

    public function lookupAll() : UserDBCollectionInterface
    {
        $this->logger->debug('Looking up all users.');
        $query = 'select * from ' . self::TABLE_NAME;
        $this->logger->debug($query);
        $res = $this->db->query($query);
        $all_users = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $all_users[] = $this->element_factory->handler()
                ->withOid($row->oid)
                ->withLogin($row->login)
                ->withPasswordStatus(Status::from((int) $row->status_pwd))
                ->withCreationStatus(Status::from((int) $row->status_created))
                ->withImportStatusFailed((bool) ((int) $row->import_failure));
        }
        return $this->element_factory->collection(...$all_users);
    }
}
