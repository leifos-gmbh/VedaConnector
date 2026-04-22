<?php

declare(strict_types=1);

use ILIAS\Cron\Schedule\CronJobScheduleType;
use Leifos\VedaConnector\Factory as VedaFactory;
use Leifos\VedaConnector\I\FactoryInterface as VedaFactoryInterface;
use Leifos\VedaConnector\I\ImporterInterface as ilVedaImporterInterface;
use Leifos\VedaConnector\I\PluginInterface;
use Leifos\VedaConnector\I\Settings\Name;

/**
 * @ilCtrl_isCalledBy ilVedaConnectorCronJob: ilObjComponentSettingsGUI
 */
class ilVedaConnectorCronJob extends ilCronJob
{
    protected VedaFactoryInterface $veda_factory;

    /**
     * ilVedaConnectorCronJob constructor.
     */
    public function __construct()
    {
        $this->veda_factory = VedaFactory::getInstance();
    }

    public function getId() : string
    {
        return $this->veda_factory->plugin()->getId();
    }

    public function getTitle() : string
    {
        return PluginInterface::PNAME;
    }

    public function getDescription() : string
    {
        return $this->veda_factory->plugin()->txt('cron_job_info');
    }

    public function getDefaultScheduleType() : CronJobScheduleType
    {
        return CronJobScheduleType::SCHEDULE_TYPE_IN_HOURS;
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
            // for 15 minutes try to import until no LockException is thrown
            $utime = time();
            while (($utime + (60 * 30)) > time()) {
                try {
                    $this->veda_factory->importer()->import(
                        ilVedaImporterInterface::IMPORT_TYPE_UNDEFINED,
                        false,
                        [
                            ilVedaImporterInterface::IMPORT_USR_ALL,
                            ilVedaImporterInterface::IMPORT_CRS,
                            ilVedaImporterInterface::IMPORT_MEM
                        ]
                    );
                    $this->veda_factory->logger()->handler()->info("Import performed successfully");
                    break;
                } catch (ilVedaImporterLockedException $e) {
                    $this->veda_factory->logger()->handler()->info('Import cronjob in execution.');
                    sleep(60);
                    $this->veda_factory->logger()->handler()->info('Slept 60 seconds. Retrying...');
                }
            }
            $this->veda_factory->settings()->handler()->writeInt(Name::CRON_LAST_EXECUTION, time());
            $this->veda_factory->mail()->handler()->sendStatus();
            $result->setStatus(ilCronJobResult::STATUS_OK);
        } catch (Exception $e) {
            $result->setStatus(ilCronJobResult::STATUS_CRASHED);
            $result->setMessage($e->getMessage());
            $this->veda_factory->logger()->handler()->warning('Cron update failed with message: ' . $e->getMessage());
        }
        return $result;
    }
}
