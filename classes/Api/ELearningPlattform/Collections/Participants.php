<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ELearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\TeilnehmerELearningPlattform;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;

class Participants implements ParticipantsInterface
{
    /**
     * @var TeilnehmerELearningPlattform[]
     */
    protected array $elearning_participants;
    protected int $index;

    public function __construct(ParticipantsInterface ...$elearning_participants)
    {
        $this->elearning_participants = $elearning_participants;
        $this->index = 0;
    }

    public function current() : TeilnehmerELearningPlattform
    {
        return $this->elearning_participants[$this->index];
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
        return isset($this->elearning_participants[$this->index]);
    }

    public function count() : int
    {
        return count($this->elearning_participants);
    }
}
