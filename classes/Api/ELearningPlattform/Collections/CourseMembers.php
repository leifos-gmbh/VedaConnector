<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ELearningPlattform\Collections;

use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Teilnehmerkurszuordnung;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseMembersInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface as UtilsInterface;

class CourseMembers implements CourseMembersInterface
{
    /**
     * @param Teilnehmerkurszuordnung[] $crs_mmbrs
     */
    protected array $crs_mmbrs;
    protected int $index;

    public function __construct(
        protected LoggerInterface  $logger,
        protected UtilsInterface $utils,
        Teilnehmerkurszuordnung ...$crs_mmbrs
    ) {
        $this->crs_mmbrs = $crs_mmbrs;
        $this->index = 0;
    }

    public function containsMemberWithOID(
        string $oid
    ) : bool {
        foreach ($this->crs_mmbrs as $member) {
            if (
                !$this->utils->compareOidsEqual($oid, $member->getTeilnehmerId()) ||
                !$this->utils->isValidDate($member->getKursZugriffAb(), $member->getKursZugriffBis())
            ) {
                continue;
            }
            return true;
        }
        return false;
    }

    public function logContent()
    {
        $this->logger->dump($this->crs_mmbrs, ilLogLevel::DEBUG);
    }

    public function current() : Teilnehmerkurszuordnung
    {
        return $this->crs_mmbrs[$this->index];
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
        return isset($this->crs_mmbrs[$this->index]);
    }

    public function rewind() : void
    {
        $this->index = 0;
    }

    public function count() : int
    {
        return count($this->crs_mmbrs);
    }
}
