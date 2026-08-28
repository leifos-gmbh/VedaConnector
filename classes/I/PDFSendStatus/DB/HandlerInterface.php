<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\CollectionInterface as ElementCollectionInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\HandlerInterface as ElementInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\HandlerInterface as KeyHandlerInterface;

interface HandlerInterface
{
    public const TABLE_NAME = "cron_crnhk_vedaimp_snd";
    public const FIELD_NAME_SEQ_ID = "seq_id";
    public const FIELD_NAME_COURSE_OID = "crs_oid";
    public const FIELD_NAME_PARTICIPANT_OID = "participant_oid";
    public const FIELD_NAME_COURSE_ID = "course_id";
    public const FIELD_NAME_PARTICIPANT_ID = "participant_id";
    public const FIELD_NAME_STATUS_PASSED = "status_passed";
    public const FIELD_NAME_TIMESTAMP_PASSED = "timestamp_passed";
    public const FIELD_NAME_STATUS_SEND = "status_send";
    public const FIELD_NAME_TIMESTAMP_SEND = "timestamp_send";
    public const FIELD_NAME_ERROR_CODE = "error_code";

    public function getByKey(
        KeyHandlerInterface $key
    ): ElementCollectionInterface;

    public function deleteByKey(
        KeyHandlerInterface $key
    ): void;

    public function updateByElement(
        ElementInterface $element
    ): void;

    public function createElement(): ElementInterface;
}
