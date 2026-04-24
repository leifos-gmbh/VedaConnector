<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB\Element;

enum Type: int
{
    case SIFA = 1;

    case STANDARD = 2;
}
