<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AufsichtspersonKurszugriff;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\SupervisorsInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Supervisors implements SupervisorsInterface
{
    /**
     * @var AufsichtspersonKurszugriff[]
     */
    protected array $education_train_supervisor;
    protected int $index;

    public function __construct(
        protected LoggerInterface $logger,
        AufsichtspersonKurszugriff ...$education_train_supervisor
    ) {
        $this->education_train_supervisor = $education_train_supervisor;
        $this->index = 0;
    }

    public function logContent() : void
    {
        $this->logger->dump($this->education_train_supervisor, ilLogLevel::DEBUG);
    }

    public function current() : AufsichtspersonKurszugriff
    {
        return $this->education_train_supervisor[$this->index];
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
        return isset($this->education_train_supervisor[$this->index]);
    }

    public function count() : int
    {
        return count($this->education_train_supervisor);
    }
}
