<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\TrainingProgram\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\AufsichtspersonKurszugriff;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugDozent;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugLernbegleiter;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugTeilnehmer;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\CompanionsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\FactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\MembersInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\SupervisorsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\TutorsInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function companions(
        AusbildungszugLernbegleiter ...$elements
    ): CompanionsInterface {
        return new Companions(
            $this->logger_factory->handler(),
            ...$elements
        );
    }

    public function members(
        AusbildungszugTeilnehmer ...$elements
    ): MembersInterface {
        return new Members(
            $this->logger_factory->handler(),
            ...$elements
        );
    }

    public function supervisors(
        AufsichtspersonKurszugriff ...$elements
    ): SupervisorsInterface {
        return new Supervisors(
            $this->logger_factory->handler(),
            ...$elements
        );
    }

    public function tutors(
        AusbildungszugDozent ...$elements
    ): TutorsInterface {
        return new Tutors(
            $this->logger_factory->handler(),
            ...$elements
        );
    }
}
