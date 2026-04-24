<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Adapter;

use ilObjectDefinition;
use ilObjUser;
use ilRbacAdmin;
use ilRbacReview;
use Leifos\VedaConnector\I\Api\Adapter\CourseImportInterface;
use Leifos\VedaConnector\I\Api\Adapter\CourseStandardImportInterface;
use Leifos\VedaConnector\I\Api\Adapter\FactoryInterface;
use Leifos\VedaConnector\I\Api\Adapter\MemberImportInterface;
use Leifos\VedaConnector\I\Api\Adapter\MemberStandardImportInterface;
use Leifos\VedaConnector\I\Api\Adapter\UserImportInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\FactoryInterface as ApiElearningPlattformFactoryInterface;
use Leifos\VedaConnector\I\Api\Organisation\FactoryInterface as ApiOrganisationFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\FactoryInterface as ApiTrainingCourseFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\FactoryInterface as ApiTrainingProgramFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgramModules\FactoryInterface as ApiTrainingProgramModulesFactoryInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseStatusDBFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\FactoryInterface as MDClaimingDBFactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\UDF\DB\FactoryInterface as UDFDBFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\FactoryInterface as UserStatusDBFactoryInterface;
use Leifos\VedaConnector\I\Utils\FactoryInterface as UtilsFactoryInterface;
use Leifos\VedaConnector\I\Exception\FactoryInterface as ExcepionFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilObjUser $user,
        protected ilObjectDefinition $object_definition,
        protected ilRbacAdmin $rbac_admin,
        protected ilRbacReview $rbac_review,
        protected LoggerFactoryInterface $logger_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected BuilderFactoryInterface $builder_factory,
        protected UserStatusDBFactoryInterface $user_status_db_factory,
        protected CourseStatusDBFactoryInterface $course_status_db_factory,
        protected MDClaimingDBFactoryInterface $md_db_factory,
        protected UDFDBFactoryInterface $udf_db_factory,
        protected ApiElearningPlattformFactoryInterface $api_elearning_plattform_factory,
        protected ApiOrganisationFactoryInterface $api_organisation_factory,
        protected ApiTrainingProgramFactoryInterface $api_training_program_factory,
        protected ApiTrainingCourseFactoryInterface $api_training_course_factory,
        protected ApiTrainingProgramModulesFactoryInterface $api_training_program_modules_factory,
        protected UtilsFactoryInterface $utils_factory,
        protected ExcepionFactoryInterface $exception_factory
    ) {
    }

    public function courseImport(): CourseImportInterface
    {
        return new CourseImport(
            $this->user,
            $this->object_definition,
            $this->rbac_admin,
            $this->rbac_review,
            $this->logger_factory->handler(),
            $this->md_db_factory->handler(),
            $this->settings_factory->handler(),
            $this->builder_factory,
            $this->api_elearning_plattform_factory->handler(),
            $this->api_training_program_factory->handler(),
            $this->api_training_course_factory->handler(),
            $this->utils_factory->handler(),
            $this->exception_factory
        );
    }

    public function courseStandardImport(): CourseStandardImportInterface
    {
        return new CourseStandardImport(
            $this->user,
            $this->object_definition,
            $this->logger_factory->handler(),
            $this->settings_factory->handler(),
            $this->builder_factory,
            $this->api_elearning_plattform_factory->handler(),
            $this->exception_factory
        );
    }

    public function userImport(): UserImportInterface
    {
        return new UserImport(
            $this->logger_factory->handler(),
            $this->settings_factory->handler(),
            $this->user_status_db_factory->handler(),
            $this->builder_factory,
            $this->api_elearning_plattform_factory->collections(),
            $this->api_elearning_plattform_factory->handler(),
            $this->api_organisation_factory->handler(),
            $this->exception_factory
        );
    }

    public function memberImport(): MemberImportInterface
    {
        return new MemberImport(
            $this->logger_factory->handler(),
            $this->rbac_admin,
            $this->rbac_review,
            $this->md_db_factory->handler(),
            $this->udf_db_factory->handler(),
            $this->builder_factory,
            $this->settings_factory->handler(),
            $this->api_training_program_modules_factory->handler(),
            $this->api_training_program_factory->handler(),
            $this->utils_factory->handler(),
            $this->exception_factory
        );
    }

    public function memberStandardImport(): MemberStandardImportInterface
    {
        return new MemberStandardImport(
            $this->logger_factory->handler(),
            $this->rbac_admin,
            $this->udf_db_factory->handler(),
            $this->course_status_db_factory->handler(),
            $this->builder_factory,
            $this->settings_factory->handler(),
            $this->api_elearning_plattform_factory->handler(),
            $this->utils_factory->handler()
        );
    }
}
