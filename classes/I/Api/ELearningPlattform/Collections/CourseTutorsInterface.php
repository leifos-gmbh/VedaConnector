<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform\Collections;

use Countable;
use Iterator;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Dozentenkurszuordnung;

interface CourseTutorsInterface extends Iterator, Countable
{
    public function logContent();

    public function containsTutorWithOId(string $oid) : bool;

    public function current() : Dozentenkurszuordnung;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
