<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgram\Collections;

use Iterator;
use Countable;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugTeilnehmer;

interface MembersInterface extends Iterator, Countable
{
    public function logContent() : void;

    public function current() : AusbildungszugTeilnehmer;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
