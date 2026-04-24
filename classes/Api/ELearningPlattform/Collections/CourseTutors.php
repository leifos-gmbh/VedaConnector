<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ElearningPlattform\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Dozentenkurszuordnung;
use Leifos\VedaConnector\I\Api\ElearningPlattform\Collections\CourseTutorsInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface as UtilsInterface;

class CourseTutors implements CourseTutorsInterface
{
    /**
     * @var Dozentenkurszuordnung[]
     */
    protected array $crs_tutors;
    protected int $index;

    public function __construct(
        protected LoggerInterface  $logger,
        protected UtilsInterface $utils,
        Dozentenkurszuordnung ...$crs_tutors
    ) {
        $this->crs_tutors = $crs_tutors;
        $this->index = 0;
    }

    public function containsTutorWithOID(
        string $oid
    ) : bool {
        foreach ($this->crs_tutors as $tutor) {
            if (
                !$this->utils->compareOidsEqual($oid, $tutor->getElearningbenutzeraccountId()) ||
                !$this->utils->isValidDate($tutor->getKursZugriffAb(), $tutor->getKursZugriffBis())
            ) {
                continue;
            }
            return true;
        }
        return false;
    }

    public function logContent()
    {
        $this->logger->dump($this->crs_tutors, ilLogLevel::DEBUG);
    }

    public function current() : Dozentenkurszuordnung
    {
        return $this->crs_tutors[$this->index];
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
        return isset($this->crs_tutors[$this->index]);
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function count() : int
    {
        return count($this->crs_tutors);
    }
}
