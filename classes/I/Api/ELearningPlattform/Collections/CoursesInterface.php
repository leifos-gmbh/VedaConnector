<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Elearningkurs;
use Iterator;
use Countable;

interface CoursesInterface extends Iterator, Countable
{
    public function current() : ELearningkurs;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;

    public function getCourseByOId(string $oid): ?Elearningkurs;
}
