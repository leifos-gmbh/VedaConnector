<?php

declare(strict_types=1);

use ILIAS\Cron\Schedule\CronJobScheduleType as CronJobScheduleType;
use Leifos\VedaConnector\Exception\ImporterLocked as ImporterLockedException;
use Leifos\VedaConnector\Factory as VedaFactory;
use Leifos\VedaConnector\I\FactoryInterface as VedaFactoryInterface;
use Leifos\VedaConnector\I\ImporterInterface as ilVedaImporterInterface;
use Leifos\VedaConnector\I\PluginInterface;
use Leifos\VedaConnector\I\Settings\Name;

/**
 * @ilCtrl_isCalledBy ilVedaConnectorCronJob: ilObjComponentSettingsGUI
 */
class ilVedaConnectorFastCronJob extends ilCronJob
{
    public const JOB_ID = 'vedaimp_fast';
    protected VedaFactoryInterface $veda_factory;

    public function __construct()
    {
        $this->veda_factory = VedaFactory::getInstance();
    }

    public function getId() : string
    {
        return self::JOB_ID;
    }

    public function getTitle() : string
    {
        return PluginInterface::PNAME;
    }

    public function getDescription() : string
    {
        return $this->veda_factory->plugin()->txt('cron_job_fast_info');
    }

    public function getDefaultScheduleType() : CronJobScheduleType
    {
        return CronJobScheduleType::SCHEDULE_TYPE_IN_MINUTES;;
    }

    public function getDefaultScheduleValue() : int
    {
        return $this->veda_factory->settings()->handler()->readAsInt(Name::CRON_INTERVAL);
    }

    public function hasAutoActivation() : bool
    {
        return false;
    }

    public function hasFlexibleSchedule() : bool
    {
        return true;
    }

    public function hasCustomSettings() : bool
    {
        return false;
    }

    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();
        try {
            $this->veda_factory->importer()->import(
                ilVedaImporterInterface::IMPORT_TYPE_UNDEFINED,
                false,
                [
                    ilVedaImporterInterface::IMPORT_USR_INCREMENTAL,
                    ilVedaImporterInterface::IMPORT_MEM
                ]
            );
            $this->veda_factory->settings()->handler()->writeInt(Name::CRON_LAST_EXECUTION, time());
            $this->veda_factory->mail()->handler()->sendStatus();
            $result->setStatus(ilCronJobResult::STATUS_OK);
        } catch (ImporterLockedException $e) {
            // Ignore this lock exception, since the main cron job might be running.
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
            $result->setMessage('Cronjob locked');
            $this->veda_factory->logger()->handler()->info('Cronjob locked');
        } catch (Exception $e) {
            $result->setStatus(ilCronJobResult::STATUS_CRASHED);
            $result->setMessage($e->getMessage());
            $this->veda_factory->logger()->handler()->warning('Cron update failed with message: ' . $e->getMessage());
        }
        return $result;
    }
}
