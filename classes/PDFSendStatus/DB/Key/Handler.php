<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Key;

use DateTimeImmutable;
use ilDBConstants;
use ilDBInterface;
use ILIAS\Data\Order;
use ILIAS\Data\Range;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\HandlerInterface as PDFSendDBInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\HandlerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\MappingInterface;

class Handler implements HandlerInterface
{
    /**
     * @var int[]
     */
    protected array $db_sequence_ids;

    /**
     * @var SendStatus[]
     */
    protected array $send_statuses;

    /**
     * @var PassedStatus[]
     */
    protected array $passed_statuses;

    /**
     * @var ErrorCode[]
     */
    protected array $error_codes;

    /**
     * @var string[]
     */
    protected array $course_oids;

    /**
     * @var string[]
     */
    protected array $participant_oids;

    protected MappingInterface $table_to_db_column_mapping;

    protected DateTimeImmutable $send_date_upper_limit;
    protected DateTimeImmutable $send_date_lower_limit;
    protected DateTimeImmutable $passed_date_upper_limit;
    protected DateTimeImmutable $passed_date_lower_limit;
    protected Order $order;
    protected Range $range;

    public function __construct(
        protected ilDBInterface $db,
    ) {
    }

    public function getWhereClause(): string
    {
        $where = "";
        if (isset($this->db_sequence_ids)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_SEQ_ID, $this->db_sequence_ids, false, ilDBConstants::T_INTEGER);
        }
        if (isset($this->participant_oids)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_PARTICIPANT_OID, $this->participant_oids, false, ilDBConstants::T_TEXT);
        }
        if (isset($this->course_oids)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_COURSE_OID, $this->course_oids, false, ilDBConstants::T_TEXT);
        }
        if (isset($this->send_statuses)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_STATUS_SEND, $this->send_statuses, false, ilDBConstants::T_INTEGER);
        }
        if (isset($this->passed_statuses)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_STATUS_PASSED, $this->passed_statuses, false, ilDBConstants::T_INTEGER);
        }
        if (isset($this->error_codes)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= $this->db->in(PDFSendDBInterface::FIELD_NAME_ERROR_CODE, $this->error_codes, false, ilDBConstants::T_INTEGER);
        }
        if (isset($this->send_date_lower_limit) && isset($this->send_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) BETWEEN %s AND %s",
                PDFSendDBInterface::FIELD_NAME_TIMESTAMP_SEND,
                $this->db->quote($this->send_date_lower_limit->format("Y-m-d"), ilDBConstants::T_DATETIME),
                $this->db->quote($this->send_date_upper_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        if (!isset($this->send_date_lower_limit) && isset($this->send_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) < %s",
                PDFSendDBInterface::FIELD_NAME_TIMESTAMP_SEND,
                $this->db->quote($this->send_date_upper_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        if (isset($this->send_date_lower_limit) && !isset($this->send_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) > %s",
                PDFSendDBInterface::FIELD_NAME_TIMESTAMP_SEND,
                $this->db->quote($this->send_date_lower_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        if (isset($this->passed_date_lower_limit) && isset($this->passed_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) BETWEEN %s AND %s",
                PDFSendDBInterface::FIELD_NAME_STATUS_PASSED,
                $this->db->quote($this->passed_date_lower_limit->format("Y-m-d"), ilDBConstants::T_DATETIME),
                $this->db->quote($this->passed_date_upper_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        if (isset($this->passed_date_lower_limit) && !isset($this->passed_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) > %s",
                PDFSendDBInterface::FIELD_NAME_STATUS_PASSED,
                $this->db->quote($this->passed_date_lower_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        if (!isset($this->passed_date_lower_limit) && isset($this->passed_date_upper_limit)) {
            $where .= strlen($where) > 0 ? " AND " : "";
            $where .= sprintf("DATE(%s) < %s",
                PDFSendDBInterface::FIELD_NAME_STATUS_PASSED,
                $this->db->quote($this->passed_date_upper_limit->format("Y-m-d"), ilDBConstants::T_DATETIME)
            );
        }
        return strlen($where) > 0 ? sprintf("WHERE %s", $where) : "";
    }

    public function getOrderClause(): string
    {
        if (!isset($this->order) || !isset($this->table_to_db_column_mapping)) {
            return '';
        }
        [$column_name, $direction] = $this->order->join([], fn($ret, $key, $value) => [$key, $value]);

        return $this->table_to_db_column_mapping->has($column_name)
            ? sprintf("ORDER BY %s %s",
                $this->db->quoteIdentifier($this->table_to_db_column_mapping->get($column_name)),
                $direction
            ) : '';
    }

    public function getRangeClause(): string
    {
        return isset($this->range)
            ? sprintf("LIMIT %s, %s",
                $this->db->quote($this->range->getStart(), ilDBConstants::T_INTEGER),
                $this->db->quote($this->range->getLength(), ilDBConstants::T_INTEGER)
            ) : "";
    }

    public function withOrder(
        Order $order,
        MappingInterface $table_to_db_column_mapping
    ): HandlerInterface {
        $clone = clone $this;
        $clone->order = $order;
        $clone->table_to_db_column_mapping = $table_to_db_column_mapping;
        return $clone;
    }

    public function getOrder(): Order
    {
        return $this->order_direction ?? new Order(PDFSendDBInterface::FIELD_NAME_SEQ_ID, Order::ASC);
    }

    public function withRange(
        Range $range
    ): HandlerInterface {
        $clone = clone $this;
        $clone->range = $range;
        return $clone;
    }

    public function getRange(): Range
    {
        return $this->range ?? new Range(0,0);
    }

    public function withDBSequenceIds(
        int ...$db_sequence_ids
    ): HandlerInterface {
        $clone = clone $this;
        $clone->db_sequence_ids = $db_sequence_ids;
        return $clone;
    }

    public function withSendStatuses(
        SendStatus ...$send_status
    ): HandlerInterface {
        $clone = clone $this;
        $clone->send_statuses = $send_status;
        return $clone;
    }

    public function withPassedStatuses(
        PassedStatus ...$passed_status
    ): HandlerInterface {
        $clone = clone $this;
        $clone->passed_statuses = $passed_status;
        return $clone;
    }

    public function withErrorCodes(
        ErrorCode ...$error_code
    ): HandlerInterface {
        $clone = clone $this;
        $clone->error_codes = $error_code;
        return $clone;
    }

    public function withCourseOIds(
        string ...$course_oids
    ): HandlerInterface {
        $clone = clone $this;
        $clone->course_oids = $course_oids;
        return $clone;
    }

    public function withParticipantOIds(
        string ...$participant_oids
    ): HandlerInterface {
        $clone = clone $this;
        $clone->participant_oids = $participant_oids;
        return $clone;
    }

    public function withSendDateBevore(
        DateTimeImmutable $date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->send_date_upper_limit = $date;
        return $clone;
    }

    public function withSendDateAfter(
        DateTimeImmutable $date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->send_date_lower_limit = $date;
        return $clone;
    }

    public function withPassedDateBevore(
        DateTimeImmutable $date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->passed_date_upper_limit = $date;
        return $clone;
    }

    public function withPassedDateAfter(
        DateTimeImmutable $date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->passed_date_lower_limit = $date;
        return $clone;
    }
}
