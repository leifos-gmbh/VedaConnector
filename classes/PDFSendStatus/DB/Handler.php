<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB;

use DateTimeImmutable;
use ilDBConstants;
use ilDBInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\CollectionInterface as ElementCollectionInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\FactoryInterface as ElementFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface as ElementInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\HandlerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface as KeyFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\HandlerInterface as KeyHandlerInterface;

readonly class Handler implements HandlerInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected KeyFactoryInterface $key_factory,
        protected ElementFactoryInterface $element_factory,
        protected LoggerInterface $logger
    ) {
    }

    public function getByKey(
        KeyHandlerInterface $key
    ): ElementCollectionInterface {
        $select_clause = sprintf("SELECT * FROM %s",
            $this->db->quoteIdentifier(HandlerInterface::TABLE_NAME)
        );
        $query = sprintf("%s %s %s %s",
            $select_clause,
            $key->getWhereClause(),
            $key->getOrderClause(),
            $key->getRangeClause()
        );
        $this->logger->debug("PDFSend getByKey(): " . $query);
        $res = $this->db->query($query);
        $elements = [];
        while ($row = $this->db->fetchAssoc($res)) {
            $element = $this->element_factory->handler()
                ->withDBSequenceId((int) ($row['seq_id'] ?? -1))
                ->withCourseOId((string) ($row['crs_oid'] ?? ""))
                ->withParticipantOId((string) ($row['participant_oid'] ?? ""))
                ->withSendStatus(SendStatus::from((int) ($row['status_send'] ?? SendStatus::NULL)))
                ->withPassedStatus(PassedStatus::from((int) ($row['status_passed'] ?? PassedStatus::NULL)))
                ->withErrorCode(ErrorCode::from((int) ($row['error_code'] ?? ErrorCode::NULL)))
                ->withCourseId((int) ($row['course_id'] ?? -1))
                ->withParticipantId((int) ($row['participant_id'] ?? -1));
            $passed_date = $row['timestamp_passed'] ?? null;
            $send_data = $row['timestamp_send'] ?? null;
            if (!is_null($passed_date)) {
                $element = $element
                    ->withPassedDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $passed_date));
            }
            if (!is_null($send_data)) {
                $element = $element
                    ->withSendDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $send_data));
            }
            $elements[] = $element;
        }
        return $this->element_factory->collection(...$elements);
    }

    public function deleteByKey(
        KeyHandlerInterface $key
    ): void {
        $query = sprintf("DELETE FROM %s %s",
            $this->db->quoteIdentifier(HandlerInterface::TABLE_NAME),
            $key->getWhereClause()
        );
        $this->logger->debug("PDFSend deleteByKey(): " . $query);
        $this->db->manipulate($query);
    }

    public function updateByElement(
        ElementInterface $element
    ): void {
        $query = sprintf("INSERT INTO %s (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s), %s=VALUES(%s)",
            $this->db->quoteIdentifier(HandlerInterface::TABLE_NAME),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_SEQ_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_ERROR_CODE),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_ID),
            $this->db->quote($element->getDBSequenceId(), ilDBConstants::T_INTEGER),
            $this->db->quote($element->getCourseOId(), ilDBConstants::T_TEXT),
            $this->db->quote($element->getParticipantOId(), ilDBConstants::T_TEXT),
            $this->db->quote($element->getPassedStatus()->value, ilDBConstants::T_INTEGER),
            $this->db->quote(is_null($element->getPassedDate()) ? null : $element->getPassedDate()->format("Y-m-d H:i:s"), ilDBConstants::T_TIMESTAMP),
            $this->db->quote($element->getSendStatus()->value, ilDBConstants::T_INTEGER),
            $this->db->quote(is_null($element->getSendDate()) ? null : $element->getSendDate()->format("Y-m-d H:i:s"), ilDBConstants::T_TIMESTAMP),
            $this->db->quote($element->getErrorCode()->value, ilDBConstants::T_INTEGER),
            $this->db->quote($element->getCourseId(), ilDBConstants::T_INTEGER),
            $this->db->quote($element->getParticipantId(), ilDBConstants::T_INTEGER),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_SEQ_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_SEQ_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_OID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_PASSED),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_STATUS_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_TIMESTAMP_SEND),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_ERROR_CODE),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_ERROR_CODE),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_COURSE_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_ID),
            $this->db->quoteIdentifier(HandlerInterface::FIELD_NAME_PARTICIPANT_ID)
        );
        $this->logger->debug("PDFSend updateByElement(): " . $query);
        $this->db->manipulate($query);
    }

    public function createElement(): ElementInterface
    {
        $next_sequence_id = $this->db->nextId(HandlerInterface::TABLE_NAME);
        $element = $this->element_factory->handler()->withDBSequenceId($next_sequence_id);
        $this->updateByElement($element);
        $this->logger->debug("Created PDFSend db element with sequence id: " . $next_sequence_id);
        return $element;
    }
}
