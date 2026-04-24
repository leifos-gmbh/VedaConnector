<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Builder;

use Leifos\VedaConnector\I\Builder\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\BuilderInterface as CourseStatusBuilderInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseStatusDBFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\BuilderInterface as MailBuilderInterface;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\BuilderInterface as TrainingProgramModulesBuilderInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\FactoryInterface as TrainingProgramModulesDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\BuilderInterface as UserStatusBuilderInterface;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserStatusDBFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected UserStatusDBFactoryInterface $user_status_db_factory,
        protected CourseStatusDBFactoryInterface $course_status_db_factory,
        protected MailDBFactoryInterface $mail_db_factory,
        protected TrainingProgramModulesDBFactoryInterface $training_program_modules_db_factory
    ) {
    }

    public function mailSegment(): MailBuilderInterface
    {
        return $this->mail_db_factory->elementBuilder();
    }

    public function courseStatus(): CourseStatusBuilderInterface
    {
        return $this->course_status_db_factory->elementBuilder();
    }

    public function userStatus(): UserStatusBuilderInterface
    {
        return $this->user_status_db_factory->elementBuilder();
    }

    public function trainingProgrammModule(): TrainingProgramModulesBuilderInterface
    {
        return $this->training_program_modules_db_factory->elementBuilder();
    }
}
