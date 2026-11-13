<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Key;

use DateTimeImmutable;
use ILIAS\Data\Order;
use ILIAS\Data\Range;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;

interface HandlerInterface
{
    public function getWhereClause(): string;

    public function getOrderClause(): string;

    public function getRangeClause(): string;

    public function withOrder(
        Order $order,
        MappingInterface $table_to_db_column_mapping
    ): HandlerInterface;

    public function withRange(
        Range $range
    ): HandlerInterface;

    public function withDBSequenceIds(
        int ...$db_sequence_ids
    ): HandlerInterface;

    public function withSendStatuses(
        SendStatus ...$send_status
    ): HandlerInterface;

    public function withPassedStatuses(
        PassedStatus ...$passed_status
    ): HandlerInterface;

    public function withErrorCodes(
        ErrorCode ...$error_code
    ): HandlerInterface;

    public function withCourseIds(
        int ...$course_ids
    ): HandlerInterface;

    public function withParticipantIds(
        int ...$participant_ids
    ): HandlerInterface;

    public function withCourseOIds(
        string ...$course_oids
    ): HandlerInterface;

    public function withParticipantOIds(
        string ...$participant_oids
    ): HandlerInterface;

    public function withSendDateBevore(
        DateTimeImmutable $date
    ): HandlerInterface;

    public function withSendDateAfter(
        DateTimeImmutable $date
    ): HandlerInterface;

    public function withPassedDateBevore(
        DateTimeImmutable $date
    ): HandlerInterface;

    public function withPassedDateAfter(
        DateTimeImmutable $date
    ): HandlerInterface;
}
