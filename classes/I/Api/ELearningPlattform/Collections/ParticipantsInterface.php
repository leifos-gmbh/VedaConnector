<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform\Collections;

use Countable;
use Iterator;
use Leifos\VedaConnector\GeneratedOpenApi\Model\TeilnehmerELearningPlattform;

interface ParticipantsInterface extends Iterator, Countable
{
    public function current() : TeilnehmerELearningPlattform;

    public function key() : int;

    public function next() : void;

    public function rewind() : void;

    public function valid() : bool;

    public function count() : int;
}
