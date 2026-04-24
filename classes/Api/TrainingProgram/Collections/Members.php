<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugTeilnehmer;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\MembersInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Members implements MembersInterface
{
    /**
     * @var AusbildungszugTeilnehmer[]
     */
    protected array $education_train_participants;
    protected int $index;

    public function __construct(
        protected LoggerInterface $logger,
        AusbildungszugTeilnehmer ...$education_train_participants
    ) {
        $this->education_train_participants = $education_train_participants;
        $this->index = 0;
    }

    public function logContent() : void
    {
        $this->logger->dump($this->education_train_participants, ilLogLevel::DEBUG);
    }

    public function current() : AusbildungszugTeilnehmer
    {
        return $this->education_train_participants[$this->index];
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
        return isset($this->education_train_participants[$this->index]);
    }

    public function count() : int
    {
        return count($this->education_train_participants);
    }
}
