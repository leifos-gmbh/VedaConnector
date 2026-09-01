<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Element;

enum ErrorCode : int
{
    case NO_ERROR = 1;
    case NULL = 2;
    case OID_CANNOT_BE_DETERMINED = 3;
    case VEDA_OBJECTS_NOT_FOUND = 4;
    case COULD_NOT_BE_SEND = 5;
    case CONTENT_COULD_NOT_BE_CREATED = 6;
    case CERTIFICATE_ID_NOT_FOUND = 7;
}
