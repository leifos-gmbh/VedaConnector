<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugLernbegleiter;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\CompanionsInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;

class Companions implements CompanionsInterface
{
    /**
     * @var AusbildungszugLernbegleiter[]
     */
    protected array $education_train_companions;
    protected int $index;

    public function __construct(
        protected LoggerInterface $logger,
        AusbildungszugLernbegleiter ...$education_train_companions
    ) {
        $this->education_train_companions = $education_train_companions;
        $this->index = 0;
    }

    public function logContent() : void
    {
        $this->logger->dump($this->education_train_companions, ilLogLevel::DEBUG);
    }

    public function current() : AusbildungszugLernbegleiter
    {
        return $this->education_train_companions[$this->index];
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
        return isset($this->education_train_companions[$this->index]);
    }

    public function count() : int
    {
        return count($this->education_train_companions);
    }
}
