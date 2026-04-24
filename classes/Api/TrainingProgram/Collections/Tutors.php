<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugDozent;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\TutorsInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Tutors implements TutorsInterface
{
    /**
     * @var AusbildungszugDozent[]
     */
    protected array $education_train_tutors;
    protected int $index;

    public function __construct(
        protected LoggerInterface $logger,
        AusbildungszugDozent ...$education_train_tutors
    ) {
        $this->education_train_tutors = $education_train_tutors;
        $this->index = 0;
    }

    public function logContent() : void
    {
        $this->logger->dump($this->education_train_tutors, ilLogLevel::DEBUG);
    }

    public function current() : AusbildungszugDozent
    {
        return $this->education_train_tutors[$this->index];
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
        return isset($this->education_train_tutors[$this->index]);
    }

    public function count() : int
    {
        return count($this->education_train_tutors);
    }
}
