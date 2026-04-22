<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB\Element;

use Leifos\VedaConnector\I\CourseStatus\DB\Element\CollectionInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\HandlerInterface;

class Factory implements CourseDBElementFactoryInterface
{

    public function handler(): HandlerInterface
    {
        return new Handler();
    }

    public function collection(
        HandlerInterface ...$handler
    ): CollectionInterface {
        return new Collection(
            $this,
            ...$handler
        );
    }
}
