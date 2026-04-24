<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB\Element;

enum Status: int
{
    case NONE = 0;

    case PENDING = 1;

    case SYNCHRONIZED = 2;

    case FAILED = 3;
}
