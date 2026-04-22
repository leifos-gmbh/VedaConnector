<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api;

use Leifos\VedaConnector\I\Api\Exception\FactoryInterface as ApiExceptionFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgramModules\FactoryInterface as ApiTrainingProgramModulesFactoryInterface;
use Leifos\VedaConnector\I\Api\Header\FactoryInterface as ApiHeaderFactoryInterface;
use Leifos\VedaConnector\I\Api\Organisation\FactoryInterface as ApiOrganisationFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingCourse\FactoryInterface as ApiTrainingCourseFactoryInterface;
use Leifos\VedaConnector\I\Api\TrainingProgram\FactoryInterface as ApiTrainingProgramFactoryInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\FactoryInterface as ApiElearningPlattformFactoryInterface;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface as ApiConfigurationFactoryInterface;
use Leifos\VedaConnector\I\Api\Client\FactoryInterface as ApiClientFactoryInterface;
use Leifos\VedaConnector\I\Api\Adapter\FactoryInterface as ApiAdapterFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function exception(): ApiExceptionFactoryInterface;

    public function trainingProgramModules(): ApiTrainingProgramModulesFactoryInterface;

    public function trainingCourse(): ApiTrainingCourseFactoryInterface;

    public function organisation(): ApiOrganisationFactoryInterface;

    public function trainingProgram(): ApiTrainingProgramFactoryInterface;

    public function eLearningPlattform(): ApiElearningPlattformFactoryInterface;

    public function header(): ApiHeaderFactoryInterface;

    public function config(): ApiConfigurationFactoryInterface;

    public function client(): ApiClientFactoryInterface;

    public function adapter(): ApiAdapterFactoryInterface;
}
