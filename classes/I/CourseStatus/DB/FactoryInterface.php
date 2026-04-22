<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\CourseStatus\DB;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function element(): CourseDBElementFactoryInterface;

    public function elementBuilder(): BuilderInterface;
}
