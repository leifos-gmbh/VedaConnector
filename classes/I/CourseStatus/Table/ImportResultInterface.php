<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\Table;

interface ImportResultInterface
{
    public function init() : void;

    public function parse() : void;
}
