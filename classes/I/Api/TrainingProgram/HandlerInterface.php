<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\TrainingProgram;

use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\CompanionsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\MembersInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\SupervisorsInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\Collections\TutorsInterface;

interface HandlerInterface
{
    public function requestCompanions(
        ?string $oid
    ) : ?CompanionsInterface;

    public function requestMembers(
        ?string $oid
    ) : ?MembersInterface;

    public function requestSupervisors(
        ?string $oid
    ) : ?SupervisorsInterface;

    public function requestTutors(
        ?string $oid
    ) : ?TutorsInterface;

    public function sendCourseCreationFailed(
        string $oid
    ) : bool;

    public function sendCourseCreated(
        string $oid
    ) : bool;

    public function sendCopyStarted(
        string $oid
    ) : bool;
}
