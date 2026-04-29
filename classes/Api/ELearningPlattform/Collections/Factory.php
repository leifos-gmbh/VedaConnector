<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\ELearningPlattform\Collections;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Ausbildungszug;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Dozentenkurszuordnung;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Elearningkurs;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Lernbegleiterkurszuordnung;
use Leifos\VedaConnector\GeneratedOpenApi\Model\TeilnehmerELearningPlattform;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Teilnehmerkurszuordnung;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseCompanionsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseMembersInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CoursesInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseTutorsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\FactoryInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\TrainingProgramCoursesInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Utils\FactoryInterface as UtilsFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LoggerFactoryInterface $logger_factroy,
        protected UtilsFactoryInterface $utils_factory
    ) {
    }

    public function courseCompanions(
        Lernbegleiterkurszuordnung ...$elements
    ): CourseCompanionsInterface {
        return new CourseCompanions(
            $this->logger_factroy->handler(),
            $this->utils_factory->handler(),
            ...$elements
        );
    }

    public function courseMembers(
        Teilnehmerkurszuordnung ...$elements
    ): CourseMembersInterface {
        return new CourseMembers(
            $this->logger_factroy->handler(),
            $this->utils_factory->handler(),
            ...$elements
        );
    }

    public function courseTutors(
        Dozentenkurszuordnung ...$elements
    ): CourseTutorsInterface {
        return new CourseTutors(
            $this->logger_factroy->handler(),
            $this->utils_factory->handler(),
            ...$elements
        );
    }

    public function trainingProgramCourses(
        Ausbildungszug ...$elements
    ): TrainingProgramCoursesInterface {
        return new TrainingProgramCourses(
            $this->utils_factory->handler(),
            ...$elements
        );
    }

    public function courses(
        Elearningkurs ...$elements
    ): CoursesInterface {
        return new Courses(...$elements);
    }

    public function participants(
        TeilnehmerELearningPlattform ...$elements
    ): ParticipantsInterface {
        return new Participants(...$elements);
    }
}
