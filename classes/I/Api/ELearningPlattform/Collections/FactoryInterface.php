<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungszug;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Dozentenkurszuordnung;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Elearningkurs;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Lernbegleiterkurszuordnung;
use Leifos\VedaConnector\GeneratedOpenApi\Model\TeilnehmerELearningPlattform;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Teilnehmerkurszuordnung;

interface FactoryInterface
{
    public function courseCompanions(
        Lernbegleiterkurszuordnung ...$elements
    ): CourseCompanionsInterface;

    public function courseMembers(
        Teilnehmerkurszuordnung ...$elements
    ): CourseMembersInterface;

    public function courseTutors(
        Dozentenkurszuordnung ...$elements
    ): CourseTutorsInterface;

    public function trainingProgramCourses(
        Ausbildungszug ...$elements
    ): TrainingProgramCoursesInterface;

    public function courses(
        Elearningkurs ...$elements
    ): CoursesInterface;

    public function participants(
        TeilnehmerELearningPlattform ...$elements
    ): ParticipantsInterface;
}
