<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB;

use DateTimeImmutable;
use DateTimeZone;
use ilDBConstants;
use ilDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\CollectionInterface as MailDBElementCollectionInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface as MailDBElementFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface as MailDBElementInterface;
use Leifos\VedaConnector\I\Mail\DB\HandlerInterface as MailDBInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type;

class Handler implements MailDBInterface
{
    public function __construct(
        protected MailDBElementFactoryInterface $mail_db_element_factory,
        protected ilDBInterface $db,
        protected LoggerInterface $logger
    ) {
    }

    public function lookupAll() : MailDBElementCollectionInterface
    {
        $this->logger->debug('Looking up mail segments.');
        $query = 'SELECT * FROM ' . self::TABLE_NAME;
        $results = $this->db->query($query);
        $elements = [];
        while ($row = $this->db->fetchAssoc($results)) {
            $elements[] = $this->mail_db_element_factory->handler((int) $row['id'])
                ->withType(Type::from($row['type']))
                ->withMessage($row['msg'])
                ->withLastModified(new DateTimeImmutable($row['modified'], new DateTimeZone('Utc')));
        }
        return $this->mail_db_element_factory->collection(...$elements);
    }

    public function write(MailDBElementInterface $element) : void
    {
        $id = $this->db->nextId(self::TABLE_NAME);
        $date_time_immutable = new DateTimeImmutable('now', new DateTimeZone('Utc'));
        $values = [
            'id' => [
                ilDBConstants::T_INTEGER,
                $id
            ],
            'msg' => [
                ilDBConstants::T_TEXT,
                $element->getMessage()
            ],
            'type' => [
                ilDBConstants::T_INTEGER,
                $element->getType()->value
            ],
            'modified' => [
                ilDBConstants::T_TIMESTAMP,
                $date_time_immutable->format('Y-m-d H:i:s')
            ]
        ];
        $this->db->insert(self::TABLE_NAME, $values);
    }

    public function delete(MailDBElementInterface $element) : void
    {
        $this->logger->debug('Deleting mail segment.');
        $query = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE '
            . 'id = ' . $this->db->quote($element->getID(), ilDBConstants::T_INTEGER);
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function deleteAll() : void
    {
        $this->logger->debug('Deleteing all mail segments');
        $query = 'DELETE FROM ' . self::TABLE_NAME;
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }
}
