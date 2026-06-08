<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Element;

enum SendStatus: int
{
    case SEND = 0;
    case NOT_SEND = 1;
    case NULL = 2;
}
