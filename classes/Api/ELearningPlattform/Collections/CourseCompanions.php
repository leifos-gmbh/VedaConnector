<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ElearningPlattform\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Lernbegleiterkurszuordnung;
use Leifos\VedaConnector\I\Api\ElearningPlattform\Collections\CourseCompanionsInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\Utils;

class CourseCompanions implements CourseCompanionsInterface
{
    /**
     * @var Lernbegleiterkurszuordnung[]
     */
    protected array $crs_supervisors;
    protected int $index;

    public function __construct(
        protected LoggerInterface $logger,
        Lernbegleiterkurszuordnung ...$crs_supervisors
    ) {
        $this->crs_supervisors = $crs_supervisors;
        $this->index = 0;
    }

    public function containsCompanionWithOID(
        string $oid
    ) : bool {
        foreach ($this->crs_supervisors as $supervisor) {
            if (
                !Utils::compareOidsEqual($oid, $supervisor->getElearningbenutzeraccountId()) ||
                !Utils::isValidDate($supervisor->getKursZugriffAb(), $supervisor->getKursZugriffBis())
            ) {
                continue;
            }
            return true;
        }
        return false;
    }

    public function logContent(): void {
        $this->logger->dump($this->crs_supervisors, ilLogLevel::DEBUG);
    }

    public function current() : Lernbegleiterkurszuordnung
    {
        return $this->crs_supervisors[$this->index];
    }

    public function next() : void
    {
        $this->index++;
    }

    public function key() : int
    {
        return $this->index;
    }

    public function valid() : bool
    {
        return isset($this->crs_supervisors[$this->index]);
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function count() : int
    {
        return count($this->crs_supervisors);
    }
}
