<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\ELearningPlattform;

use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseCompanionsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseMembersInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CourseTutorsInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\TrainingProgramCoursesInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\CoursesInterface;
use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;

interface HandlerInterface
{
    public function requestCourseCompanions(
        string $crs_oid
    ) : ?CourseCompanionsInterface;

    public function requestCourseMembers(
        string $crs_oid
    ) : ?CourseMembersInterface;

    public function requestCourseTutors(
        string $crs_oid
    ) : ?CourseTutorsInterface;

    public function requestTrainingCourseTrains(
        string $training_course_id
    ) : ?TrainingProgramCoursesInterface;

    public function requestCourses() : ?CoursesInterface;

    public function requestParticipants(
        bool $a_incremental = false
    ) : ?ParticipantsInterface;

    public function sendCourseCopyStarted(
        string $crs_oid
    ) : bool;

    public function sendCourseCreationFailed(
        string $crs_oid,
        string $message
    ) : bool;

    public function sendCourseCreated(
        string $crs_oid
    ) : bool;

    public function sendParticipantStartedCourseWork(
        string $crs_oid,
        string $usr_oid
    ) : bool;

    public function sendAccountCreated(
        string $participant_id
    ) : bool;

    public function sendAccountCreationFailed(
        string $usr_oid,
        string $message
    ) : bool;

    public function sendCoursePassed(
        string $crs_oid,
        string $usr_oid
    ) : bool;

    public function sendCourseFailed(
        string $crs_oid,
        string $usr_oid
    ) : bool;

    public function sendFirstLoginSuccess(
        string $usr_oid
    ) : bool;
}
