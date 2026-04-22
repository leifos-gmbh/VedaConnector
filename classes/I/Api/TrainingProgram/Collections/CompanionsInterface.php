<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgram\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugLernbegleiter;
use Iterator;
use Countable;

interface CompanionsInterface extends Iterator, Countable
{
    public function logContent() : void;

    public function current() : AusbildungszugLernbegleiter;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
