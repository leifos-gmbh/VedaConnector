<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgram\Collections;

use Countable;
use Iterator;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AufsichtspersonKurszugriff;

interface SupervisorsInterface extends Iterator, Countable
{
    public function logContent() : void;

    public function current() : AufsichtspersonKurszugriff;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
