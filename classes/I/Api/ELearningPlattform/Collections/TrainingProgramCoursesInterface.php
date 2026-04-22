<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform\Collections;

use Iterator;
use Countable;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungszug;

interface TrainingProgramCoursesInterface extends Iterator, Countable
{
    public function getByOId(string $oid) : ?Ausbildungszug;

    public function current() : Ausbildungszug;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
