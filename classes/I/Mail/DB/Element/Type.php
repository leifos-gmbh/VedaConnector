<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB\Element;

enum Type: int
{
    case NONE = 0;
    case ERROR = 1;
    case USER_UPDATED = 2;
    case USER_IMPORTED = 3;
    case COURSE_UPDATED = 4;
    case MEMBERSHIP_UPDATED = 5;
}
