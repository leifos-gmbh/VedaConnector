<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Element;

enum PassedStatus: int
{
    case PASSED = 1;
    case NOT_PASSED = 0;
    case NULL = 2;
}
