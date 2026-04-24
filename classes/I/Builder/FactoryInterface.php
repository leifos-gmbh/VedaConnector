<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Builder;

use Leifos\VedaConnector\I\CourseStatus\DB\BuilderInterface as CourseStatusBuilderInterface;
use Leifos\VedaConnector\I\Mail\DB\BuilderInterface as MailBuilderInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\BuilderInterface as TrainingProgramModulesBuilderInterface;
use Leifos\VedaConnector\I\UserStatus\DB\BuilderInterface as UserStatusBuilderInterface;

interface FactoryInterface
{
    public function mailSegment(): MailBuilderInterface;

    public function courseStatus(): CourseStatusBuilderInterface;

    public function userStatus(): UserStatusBuilderInterface;

    public function trainingProgrammModule(): TrainingProgramModulesBuilderInterface;
}
