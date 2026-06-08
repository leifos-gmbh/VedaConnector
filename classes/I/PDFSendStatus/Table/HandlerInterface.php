<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\Table;

interface HandlerInterface
{
    public const ALL_OBJECTS = "ALL_OBJECTS";
    public const TABLE_ID = "send_status";
    public const ROW_ID = "row_ids";
    public const TABLE_COL_COURSE_OID = "tr_crs_oid";
    public const TABLE_COL_PARTICIPANT_OID = "tr_participant_oid";
    public const TABLE_COL_PASSED_STATUS = "tr_passed_status";
    public const TABLE_COL_PASSED_DATE = "tr_passed_date";
    public const TABLE_COL_SEND_STATUS = "tr_send_status";
    public const TABLE_COL_SEND_DATE = "tr_send_date";
    public const TABLE_COL_ERROR_CODE = "tr_error_code";
    public const LNG_TABLE_COL_COURSE_OID = "tr_crs_oid";
    public const LNG_TABLE_COL_PARTICIPANT_OID = "tr_participant_oid";
    public const LNG_TABLE_COL_PASSED_STATUS = "tr_passed_status";
    public const LNG_TABLE_COL_PASSED_DATE = "tr_passed_date";
    public const LNG_TABLE_COL_SEND_STATUS = "tr_send_status";
    public const LNG_TABLE_COL_SEND_DATE = "tr_send_date";
    public const LNG_TABLE_COL_ERROR_CODE = "tr_error_code";
    public const LNG_TABLE_NAME = "send_status_table_name";

    public function handleCommands(): void;

    public function getHTML(): string;
}
