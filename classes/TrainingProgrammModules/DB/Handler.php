<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules\DB;

use ilDBConstants;
use ilDBInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\HandlerInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface as TrainingProgramModulesDBElementInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\FactoryInterface as TrainingProgramModulesDBElementFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\Type;

class Handler implements HandlerInterface
{
    public function __construct(
        protected TrainingProgramModulesDBElementFactoryInterface $element_factory,
        protected ilDBInterface $db,
        protected LoggerInterface $logger
    ) {
    }

    public function update(
        TrainingProgramModulesDBElementInterface $element
    ): void {
        $this->logger->debug('Updating segment with oid: ' . $element->getOID());
        $query = sprintf('INSERT INTO %s (oid, type) values(%s, %s) ON DUPLICATE KEY UPDATE oid=oid, type=type',
            $this->db->quoteIdentifier(self::TABLE_NAME),
            $this->db->quote($element->getOID(), ilDBConstants::T_TEXT),
            $this->db->quote($element->getType(), ilDBConstants::T_TEXT)
        );
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function deleteByOId(
        string $oid
    ): void {
        $this->logger->debug('Deleting segment with oid: ' . $oid);
        $query = sprintf('delete from %s where oid = %s',
            $this->db->quoteIdentifier(self::TABLE_NAME),
            $this->db->quote($oid, ilDBConstants::T_TEXT)
        );
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function lookupByOId(
        string $oid
    ): ?TrainingProgramModulesDBElementInterface {
        $this->logger->debug('Looking up segment with oid: ' . $oid);
        $query = sprintf('select type from %s where oid = %s',
            $this->db->quoteIdentifier(self::TABLE_NAME),
            $this->db->quote($oid, ilDBConstants::T_TEXT)
        );
        $this->logger->debug($query);
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $this->element_factory->handler()
                ->withOId($oid)
                ->withType(Type::from($row->type));
        }
        return null;
    }
}
