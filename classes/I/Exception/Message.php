<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Exception;

enum Message: string
{
    case NULL = '';
    case ERR_MISSING = 'err_claiming_missing';
    case ERR_MISSING_UDF = 'err_udf_claiming_missing';
    case ERR_LOGIN_FAILED = 'exception_login_failed';
    case ERR_API = 'exception_api_call';
    case ERR_SOAP_CONNECTION = 'exception_soap_connection';
    case ERR_IMPORT_LOCKED = 'error_import_locked';

    /*
    public const ERR_LOGIN_FAILED = 1;
	public const ERR_API = 2;
    public const ERR_SOAP_CONNECTION = 3;
	public const ERR_MISSING = 1;
	public const ERR_MISSING_UDF = 2;
    */
}
