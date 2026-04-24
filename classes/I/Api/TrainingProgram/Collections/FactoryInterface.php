<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgram\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\AufsichtspersonKurszugriff;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugDozent;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugLernbegleiter;
use Leifos\VedaConnector\GeneratedOpenApi\Model\AusbildungszugTeilnehmer;

interface FactoryInterface
{
    public function companions(
        AusbildungszugLernbegleiter ...$elements
    ): CompanionsInterface;

    public function members(
        AusbildungszugTeilnehmer ...$elements
    ): MembersInterface;

    public function supervisors(
        AufsichtspersonKurszugriff ...$elements
    ): SupervisorsInterface;

    public function tutors(
        AusbildungszugDozent ...$elements
    ): TutorsInterface;
}
