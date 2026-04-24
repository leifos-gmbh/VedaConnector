<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus;

use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as DBFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\FactoryInterface as TableFactoryInterface;

interface FactoryInterface
{
    public function db(): DBFactoryInterface;

    public function table(): TableFactoryInterface;
}
