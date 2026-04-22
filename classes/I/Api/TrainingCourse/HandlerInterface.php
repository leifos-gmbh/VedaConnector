<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingCourse;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungsgang;

interface HandlerInterface
{
    public function getCourse(string $training_course_id) : ?Ausbildungsgang;
}
