<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ELearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungszug;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\TrainingProgramCoursesInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface as UtilsHandler;

class TrainingProgramCourses implements TrainingProgramCoursesInterface
{
    /**
     * @var Ausbildungszug[]
     */
    protected array $education_trains;
    protected int $index;

    public function __construct(
        protected UtilsHandler $utils,
        Ausbildungszug ...$education_trains
    ) {
        $this->education_trains = $education_trains;
        $this->index = 0;
    }

    public function getByOID(
        string $oid
    ) : ?Ausbildungszug {
        foreach ($this->education_trains as $train) {
            if ($this->utils->compareOidsEqual($train->getOid(), $oid)) {
                return $train;
            }
        }
        return null;
    }

    public function current() : Ausbildungszug
    {
        return $this->education_trains[$this->index];
    }

    public function key() : int
    {
        return $this->index;
    }

    public function next() : void
    {
        $this->index++;
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function valid() : bool
    {
        return isset($this->education_trains[$this->index]);
    }

    public function count() : int
    {
        return count($this->education_trains);
    }
}
