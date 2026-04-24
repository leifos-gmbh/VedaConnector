<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ElearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Elearningkurs;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CoursesInterface;

class Courses implements CoursesInterface
{
    /**
     * @var Elearningkurs[]
     */
    protected array $elearning_courses;
    protected int $index;

    public function __construct(
        Elearningkurs ...$elearning_courses
    ) {
        $this->elearning_courses = $elearning_courses;
        $this->index = 0;
    }

    public function current() : Elearningkurs
    {
        return $this->elearning_courses[$this->index];
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
        return isset($this->elearning_courses[$this->index]);
    }

    public function count() : int
    {
        return count($this->elearning_courses);
    }

    public function getCourseByOId(
        string $oid
    ): ?Elearningkurs {
        foreach ($this->elearning_courses as $elearning_cours) {
            if ($elearning_cours->getOid() === $oid) {
                return $elearning_cours;
            }
        }
        return null;
    }
}
