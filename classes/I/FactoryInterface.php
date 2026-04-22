<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I;

use Leifos\VedaConnector\I\Mail\FactoryInterface as MailFactoryInterface;
use Leifos\VedaConnector\I\InputFields\FactoryInterface as InputFieldsFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\FactoryInterface as UserFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\FactoryInterface as CourseFactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\FactoryInterface as TrainingProgramModulesFactoryInterface;
use Leifos\VedaConnector\I\Api\FactoryInterface as ApiFactoryInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\FactoryInterface as MDClaimingFactoryInterface;

interface FactoryInterface
{
    public static function getInstance(): FactoryInterface;

    public function plugin(): PluginInterface;

    public function importer(): ImporterInterface;

    public function utils(): UtilsInterface;

    public function logger(): LoggerFactoryInterface;

    public function userStatus(): UserFactoryInterface;

    public function coursStatus(): CourseFactoryInterface;

    public function trainingProgrammModules(): TrainingProgramModulesFactoryInterface;

    public function settings(): SettingsFactoryInterface;

    public function validator(
        int $reference_id
    ): IdValidatorInterface;

    public function mail(): MailFactoryInterface;

    public function inputFields(): InputFieldsFactoryInterface;

    public function api(): ApiFactoryInterface;

    public function builder(): BuilderFactoryInterface;

    public function mdClaiming(): MDClaimingFactoryInterface;
}
