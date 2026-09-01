<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Element;

use DateTimeImmutable;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;

class Handler implements HandlerInterface
{
    protected int $course_id;
    private int $participant_id;
    protected int $db_sequence_id;
    protected string $course_oid;
    protected string $participant_oid;
    protected PassedStatus $passed_status;
    protected ErrorCode $error_code;
    protected SendStatus $send_status;
    protected ?DateTimeImmutable $send_date;
    protected ?DateTimeImmutable $passed_date;

    public function __construct()
    {
        $this->course_oid = "";
        $this->participant_oid = "";
        $this->passed_status = PassedStatus::NULL;
        $this->send_status = SendStatus::NULL;
        $this->error_code = ErrorCode::NULL;
        $this->send_date = null;
        $this->passed_date = null;
    }

    public function getDBSequenceId(): int
    {
        return $this->db_sequence_id;
    }

    public function withDBSequenceId(
        int $db_sequence_id
    ): HandlerInterface {
        $clone = clone $this;
        $clone->db_sequence_id = $db_sequence_id;
        return $clone;
    }

    public function getCourseOId(): string
    {
        return $this->course_oid;
    }

    public function withCourseOId(
        string $course_oid
    ): HandlerInterface {
        $clone = clone $this;
        $clone->course_oid = $course_oid;
        return $clone;
    }

    public function getParticipantOId(): string
    {
        return $this->participant_oid;
    }

    public function withParticipantOId(
        string $participant_oid
    ): HandlerInterface {
        $clone = clone $this;
        $clone->participant_oid = $participant_oid;
        return $clone;
    }

    public function getPassedStatus(): PassedStatus
    {
        return $this->passed_status;
    }

    public function withPassedStatus(
        PassedStatus $passed_status
    ): HandlerInterface {
        $clone = clone $this;
        $clone->passed_status = $passed_status;
        return $clone;
    }

    public function getPassedDate(): DateTimeImmutable|null
    {
        return $this->passed_date ?? null;
    }

    public function withPassedDate(
        DateTimeImmutable $passed_date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->passed_date = $passed_date;
        return $clone;
    }

    public function getErrorCode(): ErrorCode
    {
        return $this->error_code;
    }

    public function withErrorCode(
        ErrorCode $send_status
    ): HandlerInterface {
        $clone = clone $this;
        $clone->error_code = $send_status;
        return $clone;
    }

    public function getSendStatus(): SendStatus
    {
        return $this->send_status;
    }

    public function withSendStatus(
        SendStatus $send_status
    ): HandlerInterface {
        $clone = clone $this;
        $clone->send_status = $send_status;
        return $clone;
    }

    public function getSendDate(): DateTimeImmutable|null
    {
        return $this->send_date ?? null;
    }

    public function withSendDate(
        DateTimeImmutable $send_date
    ): HandlerInterface {
        $clone = clone $this;
        $clone->send_date = $send_date;
        return $clone;
    }

    public function getCourseId(): int
    {
        return $this->course_id;
    }

    public function withCourseId(
        int $course_id
    ): HandlerInterface {
        $clone = clone $this;
        $clone->course_id = $course_id;
        return $clone;
    }

    public function getParticipantId(): int
    {
        return $this->participant_id;
    }

    public function withParticipantId(
        int $participant_id
    ): HandlerInterface {
        $clone = clone $this;
        $clone->participant_id = $participant_id;
        return $clone;
    }
}
