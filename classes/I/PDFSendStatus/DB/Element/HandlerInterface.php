<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Element;

use DateTimeImmutable;

interface HandlerInterface
{
    public function getDBSequenceId(): int;

    public function withDBSequenceId(
        int $db_sequence_id
    ): HandlerInterface;

    public function getCourseId(): int;

    public function withCourseId(
        int $course_id
    ): HandlerInterface;

    public function getParticipantId(): int;

    public function withParticipantId(
        int $participant_id
    ): HandlerInterface;

    public function getCourseOId(): string;

    public function withCourseOId(
        string $course_oid
    ): HandlerInterface;

    public function getParticipantOId(): string;

    public function withParticipantOId(
        string $participant_oid
    ): HandlerInterface;

    public function getPassedStatus(): PassedStatus;

    public function withPassedStatus(
        PassedStatus $passed_status
    ): HandlerInterface;

    public function getPassedDate(): DateTimeImmutable|null;

    public function withPassedDate(
        DateTimeImmutable $passed_date
    ): HandlerInterface;

    public function getErrorCode(): ErrorCode;

    public function withErrorCode(
        ErrorCode $send_status
    ): HandlerInterface;

    public function getSendStatus(): SendStatus;

    public function withSendStatus(
        SendStatus $send_status
    ): HandlerInterface;

    public function getSendDate(): DateTimeImmutable|null;

    public function withSendDate(
        DateTimeImmutable $send_date
    ): HandlerInterface;
}
