<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api;

use ilObjectDefinition;
use ilObjUser;
use ilRbacAdmin;
use ilRbacReview;
use Leifos\VedaConnector\Api\Adapter\Factory as ApiAdapterFactory;
use Leifos\VedaConnector\Api\Client\Factory as ApiClientFactory;
use Leifos\VedaConnector\Api\Config\Factory as ApiConfigurationFactory;
use Leifos\VedaConnector\Api\ElearningPlattform\Factory as ApiElearningPlattformFactory;
use Leifos\VedaConnector\Api\Exception\Factory as ApiExceptionFactory;
use Leifos\VedaConnector\Api\Header\Factory as ApiHeaderFactory;
use Leifos\VedaConnector\Api\Organisation\Factory as ApiOrganisationFactory;
use Leifos\VedaConnector\Api\TrainingCourse\Factory as ApiTrainingCourseFactory;
use Leifos\VedaConnector\Api\TrainingProgram\Factory as ApiTrainingProgramFactory;
use Leifos\VedaConnector\Api\TrainingProgramModules\Factory as ApiTrainingProgramModulesFactory;
use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\I\Api\Adapter\FactoryInterface as ApiAdapterFactoryInterface;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface as ApiClientFactoryInterface;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface as ApiConfigurationFactoryInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\FactoryInterface as ApiElearningPlattformFactoryInterface;
use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\FactoryInterface;
use Leifos\VedaConnector\I\Api\HandlerInterface;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface as ApiHeaderFactoryInterface;
use Leifos\VedaConnector\I\Api\Organisation\FactoryInterface as ApiOrganisationFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\FactoryInterface as ApiTrainingCourseFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\FactoryInterface as ApiTrainingProgramFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgramModules\FactoryInterface as ApiTrainingProgramModulesFactoryInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface as CourseStatusDBFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\FactoryInterface as MDClaimingDBFactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingNames;
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
        protected MailDBFactoryInterface $mail_db_factory,
        protected SettingsFactoryInterface $settings_factory,
        protected BuilderFactoryInterface $builder_factory,
        protected UserStatusDBFactoryInterface $user_status_db_factory,
        protected CourseStatusDBFactoryInterface $course_status_db_factory,
        protected MDClaimingDBFactoryInterface $md_db_factory,
        protected UDFDBFactoryInterface $udf_db_factory,
        protected UtilsFactoryInterface $utils,
        protected ExcepionFactoryInterface $exception_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->adapter()->courseImport(),
            $this->adapter()->courseStandardImport(),
            $this->adapter()->memberImport(),
            $this->adapter()->memberStandardImport(),
            $this->adapter()->userImport(),
            $this->builder_factory,
            $this->logger_factory->handler(),
            $this->md_db_factory->handler(),
            $this->user_status_db_factory->handler(),
            $this->course_status_db_factory->handler(),
            $this->eLearningPlattform()->handler(),
            $this->trainingProgram()->handler(),
            $this->trainingCourse()->handler(),
            $this->utils->handler()
        );
    }

    public function exception(): ApiExceptionFactoryInterface
    {
        return new ApiExceptionFactory(
            $this->logger_factory->handler(),
            $this->mail_db_factory
        );
    }

    public function trainingProgramModules(): ApiTrainingProgramModulesFactoryInterface
    {
        return new ApiTrainingProgramModulesFactory(
            $this->logger_factory,
            $this->exception(),
            $this->header(),
            $this->config(),
            $this->client()
        );
    }

    public function trainingCourse(): ApiTrainingCourseFactoryInterface
    {
        return new ApiTrainingCourseFactory(
            $this->exception(),
            $this->header(),
            $this->config(),
            $this->client()
        );
    }

    public function organisation(): ApiOrganisationFactoryInterface
    {
        return new ApiOrganisationFactory(
            $this->logger_factory->handler(),
            $this->exception(),
            $this->header(),
            $this->config(),
            $this->client()
        );
    }

    public function trainingProgram(): ApiTrainingProgramFactoryInterface
    {
        return new ApiTrainingProgramFactory(
            $this->logger_factory,
            $this->settings_factory,
            $this->exception(),
            $this->header(),
            $this->config(),
            $this->client()
        );
    }

    public function eLearningPlattform(): ApiElearningPlattformFactoryInterface
    {
        return new ApiElearningPlattformFactory(
            $this->logger_factory,
            $this->settings_factory,
            $this->exception(),
            $this->header(),
            $this->config(),
            $this->client(),
            $this->utils
        );
    }

    public function header(): ApiHeaderFactoryInterface
    {
        return new ApiHeaderFactory(
            $this->settings_factory
        );
    }

    public function config(): ApiConfigurationFactoryInterface
    {
        return new ApiConfigurationFactory(
            $this->settings_factory
        );
    }

    public function client(): ApiClientFactoryInterface
    {
        return new ApiClientFactory();
    }

    public function configuration(): Configuration
    {
        $config = new Configuration();
        $config->setApiKey(
            SettingNames::HEADER_TOKEN->value,
            $this->settings_factory->handler()->read(SettingNames::REST_TOKEN)
        );
        $config->setHost($this->settings_factory->handler()->read(SettingNames::REST_URL));
        $config->setAccessToken($this->settings_factory->handler()->read(SettingNames::REST_TOKEN));
        return $config;
    }

    public function adapter(): ApiAdapterFactoryInterface
    {
        return new ApiAdapterFactory(
            $this->user,
            $this->object_definition,
            $this->rbac_admin,
            $this->rbac_review,
            $this->logger_factory,
            $this->settings_factory,
            $this->builder_factory,
            $this->user_status_db_factory,
            $this->course_status_db_factory,
            $this->md_db_factory,
            $this->udf_db_factory,
            $this->eLearningPlattform(),
            $this->organisation(),
            $this->trainingProgram(),
            $this->trainingCourse(),
            $this->trainingProgramModules(),
            $this->utils,
            $this->exception_factory
        );
    }
}
