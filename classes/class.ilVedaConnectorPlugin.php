<?php

declare(strict_types=1);

use Leifos\VedaConnector\I\PluginInterface;

class ilVedaConnectorPlugin extends ilCronHookPlugin implements ilAppEventListener, PluginInterface
{
    protected const COURSE_SERVICE = 'components/ILIAS/Course';
    protected const USER_SERVICE = 'components/ILIAS/User';
    protected const OBJECT_SERVICE = 'components/ILIAS/Object';
    protected const TRACKING_SERVICE = 'components/ILIAS/Tracking';
    protected const CERTIFICATE_SERVICE = 'components/ILIAS/Certificate';
    protected const EVENT_UPDATE_PASSWORD = 'passwordChanged';
    protected const EVENT_CERTIFICATE_ISSUED = 'certificateIssued';
    protected const EVENT_DELETE_USER = 'deleteUser';
    protected const EVENT_AFTER_CLONING = 'afterCloning';
    protected const EVENT_AFTER_CLONING_DEPENDENCIES = 'afterCloningDependencies';
    protected const EVENT_UPDATE_STATUS = 'updateStatus';
    protected const EVENT_PASSED_COURSE = 'participantHasPassedCourse';
    protected const EVENT_ADD_PARTICIPANT = 'addParticipant';
    protected const EVENT_ASSIGN_USER = 'assignUser';

    protected static ?ilVedaConnectorPlugin $instance;
    protected ilComponentFactory $component_factory;

    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $DIC;
        $this->component_factory = $DIC['component.factory'];
        parent::__construct($db, $component_repository, $id);
    }

    public static function getInstance(): ilVedaConnectorPlugin
    {
        global $DIC;
        if (isset(self::$instance)) {
            return self::$instance;
        }
        /** @var ilComponentFactory $component_factory */
        $component_factory = $DIC["component.factory"];
        /** @var ilVedaConnectorPlugin $plugin */
        $plugin = $component_factory->getPlugin(PluginInterface::PLUGIN_ID);
        return $plugin;
    }

    public function getPluginName() : string
    {
        return PluginInterface::PNAME;
    }

    /**
     * @return ilVedaConnectorCronJob[]
     */
    public function getCronJobInstances() : array
    {
        return [
            new ilVedaConnectorFastCronJob(),
            new ilVedaConnectorCronJob()
        ];
    }

    public function getCronJobInstance(string $jobId) : ilCronJob
    {
        if (strcmp($jobId, ilVedaConnectorPlugin::getInstance()->getId()) == 0) {
            return new ilVedaConnectorCronJob();
        } else {
            return new ilVedaConnectorFastCronJob();
        }
    }

    public static function handleEvent(string $a_component, string $a_event, array $a_parameter): void
    {
        $plugin = self::getInstance();
        $veda_factory = new \Leifos\VedaConnector\Factory();
        $api = $veda_factory->api()->handler();
        $user_db_manager = $veda_factory->userStatus()->db()->handler();
        if (
            $a_component == self::USER_SERVICE &&
            $a_event == self::EVENT_UPDATE_PASSWORD
        ) {
            $api->handlePasswordChanged((int) $a_parameter['usr_id']);
        }
        if (
            $a_component == self::USER_SERVICE &&
            $a_event == self::EVENT_DELETE_USER
        ) {
            $user_db_manager->deleteById((int) $a_parameter['usr_id']);
        }
        if (
            $a_component == self::OBJECT_SERVICE &&
            $a_event == self::EVENT_AFTER_CLONING
        ) {
            $api->handleAfterCloningSIFAEvent(
                (int) $a_parameter['source_id'],
                (int) $a_parameter['target_id'],
                (int) $a_parameter['copy_id']
            );
            $api->handleAfterCloningStandardEvent(
                (int) ['source_id'],
                (int) $a_parameter['target_id'],
                (int) $a_parameter['copy_id']
            );
        }
        if (
            $a_component == self::OBJECT_SERVICE &&
            $a_event == self::EVENT_AFTER_CLONING_DEPENDENCIES
        ) {
            $api->handleAfterCloningDependenciesSIFAEvent(
                (int) $a_parameter['source_id'],
                (int) $a_parameter['target_id'],
                (int) $a_parameter['copy_id']
            );
            $api->handleAfterCloningDependenciesStandardEvent(
                (int) $a_parameter['source_id'],
                (int) $a_parameter['target_id'],
                (int) $a_parameter['copy_id']
            );
        }
        if (
            $a_component == self::TRACKING_SERVICE &&
            $a_event == self::EVENT_UPDATE_STATUS
        ) {
            $api->handleTrackingEvent(
                (int) $a_parameter['obj_id'],
                (int) $a_parameter['usr_id'],
                (int) $a_parameter['status']
            );
        }
        if (
            $a_component == self::CERTIFICATE_SERVICE &&
            $a_event == self::EVENT_CERTIFICATE_ISSUED
        ) {
            $api->handleCertificateIssuedEvent(
                $a_parameter['certificate']
            );
        }
        if (
            $a_component == self::OBJECT_SERVICE
        ) {
            $api->handleCloningFailed();
        }
    }
}
