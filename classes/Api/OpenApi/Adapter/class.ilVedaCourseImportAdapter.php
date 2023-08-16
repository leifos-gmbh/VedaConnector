<?php

use OpenApi\Client\Model\Ausbildungszug;

/**
 * Course import adapater
 */
class ilVedaCourseImportAdapter
{
    protected const CP_INFO_AUSBILDUNGSGANG = 1;
    protected const CP_INFO_AUSBILDUNGSZUG = 2;
    protected const CP_INFO_NAME = 3;

    protected const COPY_ACTION_COPY = 'COPY';
    protected const COPY_ACTION_LINK = 'LINK';

    protected ilLogger $logger;
    protected ilVedaConnectorSettings $settings;
    protected ilObjUser $user;
    protected ilObjectDefinition $object_definition;
    protected ilRbacAdmin $rbac_admin;
    protected ilRbacReview $rbac_review;
    protected ilVedaCourseBuilderFactoryInterface $crs_builder_factory;
    protected ilVedaSegmentRepositoryInterface $segment_repo;
    protected ilVedaMDClaimingPluginDBManagerInterface $md_db_manager;
    protected ilVedaConnector $veda_connector;
    protected ilVedaMailSegmentBuilderFactoryInterface $mail_segment_builder_factory;

    public function __construct(
        ilObjUser $user,
        ilObjectDefinition $object_definition,
        ilRbacAdmin $rbac_admin,
        ilRbacReview $rbac_review,
        ilLogger $veda_logger,
        ilVedaConnector $veda_connector,
        ilVedaCourseBuilderFactoryInterface $crs_builder_factory,
        ilVedaSegmentRepositoryInterface $segment_repo,
        ilVedaMDClaimingPluginDBManagerInterface $md_db_manager,
        ilVedaConnectorSettings $veda_settings,
        ilVedaMailSegmentBuilderFactoryInterface $mail_segment_builder_factory
    ) {
        $this->user = $user;
        $this->object_definition = $object_definition;
        $this->rbac_admin = $rbac_admin;
        $this->rbac_review = $rbac_review;
        $this->logger = $veda_logger;
        $this->settings = $veda_settings;
        $this->crs_builder_factory = $crs_builder_factory;
        $this->segment_repo = $segment_repo;
        $this->md_db_manager = $md_db_manager;
        $this->veda_connector = $veda_connector;
        $this->mail_segment_builder_factory = $mail_segment_builder_factory;
    }

    /**
     * Import "trains"
     * @throws ilVedaConnectionException
     */
    public function import() : void
    {
        foreach ($this->md_db_manager->findTrainingCourseTemplates() as $ref_id) {
            $this->importTrainingCourse($ref_id);
        }
    }

    /**
     * @throws ilVedaConnectionException
     */
    protected function importTrainingCourse(int $ref_id) : void
    {
        $training_course_id = $this->md_db_manager->findTrainingCourseId($ref_id);
        $this->logger->debug('Importing ref_id: ' . $ref_id . ' with training course id: ' . $training_course_id);
        $trains = $this->veda_connector->getElearningPlattformApi()->requestTrainingCourseTrains($training_course_id);
        foreach ($trains as $train) {
            $this->handleTrainingCourseTrainUpdate($ref_id, $train);
        }
    }

    protected function handleTrainingCourseTrainUpdate(int $source_id, Ausbildungszug $train) : void
    {
        // check if alread imported
        $train_id = $this->md_db_manager->findTrainingCourseTrain($train->getOid());
        if ($train_id) {
            $this->logger->info('Ignoring oid: ' . $train->getOid() . ' => "Ausbildungszug" already imported');
            return;
        }
        $message = 'Creating new "Ausbildungszug with oid: ' . $train->getOid();
        $this->logger->info($message);
        $this->copyTrainingCourse($source_id, $train);
        $this->mail_segment_builder_factory->buildSegment()
            ->withType(ilVedaMailSegmentType::COURSE_UPDATED)
            ->withMessage($message)
            ->store();
    }

    /**
     * @param $crs_info
     * @param $parent_id
     * @return bool|int|mixed
     * @throws ilDatabaseException
     * @throws ilObjectNotFoundException
     * @throws ilSaxParserException
     */
    protected function copyTrainingCourse(int $ref_id, Ausbildungszug $train) : int
    {
        $parent_id = $this->settings->getSifaImportDirectory();

        $copy_writer = new ilXmlWriter();
        $copy_writer->xmlStartTag(
            'Settings',
            array(
                'source_id' => $ref_id,
                'target_id' => $parent_id,
                'default_action' => 'COPY'
            )
        );

        $node_data = $GLOBALS['DIC']->repositoryTree()->getNodeData($ref_id);
        foreach ($GLOBALS['DIC']->repositoryTree()->getSubTree($node_data, true) as $node_info) {
            $default_action = self::COPY_ACTION_COPY;

            if (!$this->object_definition->allowCopy($node_info['type'])) {
                $this->logger->notice('Copying is not supported for object type: ' . $node_info['type']);
                $this->logger->notice('Changing action to "LINK"');
                $default_action = self::COPY_ACTION_LINK;
            }

            if ($node_info['type'] === 'lm') {
                $this->logger->info('Copy action for lms changed to LINK');
                $default_action = self::COPY_ACTION_LINK;
            }

            $copy_writer->xmlElement(
                'Option',
                array(
                    'id' => $node_info['ref_id'],
                    'action' => $default_action
                )
            );
        }

        $copy_writer->xmlEndTag('Settings');

        include_once './webservice/soap/classes/class.ilCopyWizardSettingsXMLParser.php';
        $xml_parser = new ilCopyWizardSettingsXMLParser($copy_writer->xmlDumpMem(false));
        try {
            $xml_parser->startParsing();
        } catch (ilSaxParserException $se) {
            $this->logger->error($se->getMessage());
            throw $se;
        }

        $options = $xml_parser->getOptions();

        $source_object = ilObjectFactory::getInstanceByRefId($ref_id);
        if ($source_object instanceof ilObjCourse) {
            $session_id = $GLOBALS['DIC']['ilAuthSession']->getId();
            $client_id = CLIENT_ID;

            // Save wizard options
            $copy_id = ilCopyWizardOptions::_allocateCopyId();
            $wizard_options = ilCopyWizardOptions::_getInstance($copy_id);
            $wizard_options->saveOwner($this->user->getId());
            $wizard_options->saveRoot($ref_id);

            $copy_info = [
                self::CP_INFO_AUSBILDUNGSGANG => $train->getAusbildungsgangId(),
                self::CP_INFO_AUSBILDUNGSZUG => $train->getOid(),
                self::CP_INFO_NAME => $train->getName()
            ];

            $wizard_options->saveTrainingCourseInfo($copy_info);

            // add entry for source container
            $wizard_options->initContainer($ref_id, $parent_id);

            foreach ($options as $source_id => $option) {
                $wizard_options->addEntry($source_id, $option);
            }
            $wizard_options->read();
            $wizard_options->storeTree($ref_id);

            $new_session_id = ilSession::_duplicate($session_id);
            $soap_client = new ilSoapClient();
            $soap_client->setResponseTimeout(600);
            $soap_client->enableWSDL(true);

            // Add new entry for oid
            $this->crs_builder_factory->buildCourse()
                ->withOID($train->getOid())
                ->withType(ilVedaCourseType::SIFA)
                ->withModified(time())
                ->withStatusCreated(ilVedaCourseStatus::PENDING)
                ->store();

            // send copy start
            try {
                $this->veda_connector->getEducationTrainApi()->sendCopyStarted($train->getOid());
            } catch (Exception $e) {
                $this->logger->error('Sending course copy start message failed with message: ' . $e->getMessage());
            }

            if ($soap_client->init()) {
                ilLoggerFactory::getLogger('obj')->info('Calling soap clone method');
                $soap_client->call('ilClone', array($new_session_id . '::' . $client_id, $copy_id));
            } else {
                $this->logger->error('Copying failed: soap init failed');
            }
        }
        return 0;
    }

    public function handleAfterCloningDependenciesEvent(int $source_id, int $target_id, int $copy_id) : void
    {
        $this->logger->debug(
            'Handling afterCloning event for for source_id: ' . $source_id .
            ' of type: ' . ilObject::_lookupType($source_id, true)
        );

        $options = ilCopyWizardOptions::_getInstance($copy_id);
        $tc = $options->getTrainingCourseInfo();

        if (!is_array($tc) || !count($tc) || !isset($tc[self::CP_INFO_AUSBILDUNGSGANG])) {
            $this->logger->debug('Ignoring non training course copy');
            return;
        }

        $mail_manager = new ilVedaMailManager();
        $mail_manager->sendSIFACourseCompleted();

        $train = $this->readTrainingCourseTrainFromCopyInfo($tc);
        if (!$train instanceof Ausbildungszug) {
            $this->logger->notice('Reading remote info failed.');
            $this->logger->dump($train, ilLogLevel::NOTICE);
            return;
        }

        $source = ilObjectFactory::getInstanceByRefId($source_id, false);
        if ($source instanceof ilObjCourse) {
            $target = ilObjectFactory::getInstanceByRefId($target_id, false);
            if ($target instanceof ilObjCourse) {
                $this->updateCourseCreatedStatus($train->getOid());
                $this->copyAdminsFromSourceToTarget($source, $target);
            } else {
                $this->logger->notice('Target should be course type: ' . $target_id);
            }
        } else {
            $this->logger->debug('Nothing todo for non-course copy.');
        }
    }

    /**
     * @return Ausbildungszug
     */
    protected function readTrainingCourseTrainFromCopyInfo(array $info) : ?Ausbildungszug
    {
        try {
            $trains = $this->veda_connector->getElearningPlattformApi()->requestTrainingCourseTrains(
                $info[self::CP_INFO_AUSBILDUNGSGANG]
            );
            foreach ($trains as $train) {
                if (ilVedaUtils::compareOidsEqual($train->getOid(), $info[self::CP_INFO_AUSBILDUNGSZUG])) {
                    return $train;
                }
            }
            $this->logger->warning('Cannot read training course train for training course id: ' . $info[self::CP_INFO_AUSBILDUNGSZUG]);
            return null;
        } catch (ilVedaConnectionException $e) {
            $this->logger->error('Cannot read training course train for training course id: ' . $info[self::CP_INFO_AUSBILDUNGSGANG]);
        }
        return null;
    }

    protected function updateCourseCreatedStatus(string $oid)
    {
        try {
            $this->veda_connector->getEducationTrainApi()->sendCourseCreated($oid);
            $this->crs_builder_factory->buildCourse()
                ->withOID($oid)
                ->withType(ilVedaCourseType::SIFA)
                ->withStatusCreated(ilVedaCourseStatus::SYNCHRONIZED)
                ->withModified(time())
                ->store();
        } catch (ilVedaConnectionException $e) {
            $this->logger->error('Cannot send course creation status');
        }
    }

    protected function copyAdminsFromSourceToTarget(ilObjCourse $source, ilObjCourse $target) : void
    {
        $source_part = ilParticipants::getInstance($source->getRefId());
        $target_part = ilParticipants::getInstance($target->getRefId());

        if (
            (!$target_part instanceof ilCourseParticipants) ||
            (!$source_part instanceof ilCourseParticipants)
        ) {
            $message = 'Cannot instantiate participants for course: ' . $source->getRefId() . ' ' . $target->getRefId();
            $this->logger->warning($message);
            $this->mail_segment_builder_factory->buildSegment()
                ->withMessage($message)
                ->withType(ilVedaMailSegmentType::ERROR)
                ->store();
            return;
        }

        foreach ($source_part->getAdmins() as $admin_id) {
            $target_part->add($admin_id, ilCourseConstants::CRS_ADMIN);
        }
    }

    public function handleAfterCloningEvent(int $a_source_id, int $a_target_id, int $a_copy_id) : void
    {
        $this->logger->debug(
            'Handling afterCloning event for for source_id: ' . $a_source_id .
            ' of type: ' . ilObject::_lookupType($a_source_id, true)
        );

        $options = ilCopyWizardOptions::_getInstance($a_copy_id);
        $tc = $options->getTrainingCourseInfo();

        if (!is_array($tc) || !count($tc) || !isset($tc[self::CP_INFO_AUSBILDUNGSGANG])) {
            $this->logger->debug('Ignoring non training course copy');
            return;
        }

        $this->logger->dump($tc);

        $train = $this->readTrainingCourseTrainFromCopyInfo($tc);
        if (!$train instanceof Ausbildungszug) {
            return;
        }

        $source = ilObjectFactory::getInstanceByRefId($a_source_id, false);
        if ($source instanceof ilObjCourse) {
            // update md id
            $this->migrateTrainingCoursetoTrain($a_source_id, $a_target_id, $train);

            $target = ilObjectFactory::getInstanceByRefId($a_target_id, false);
            if ($target instanceof ilObjCourse) {
                $this->crs_builder_factory->buildCourse()
                    ->withOID($train->getOid())
                    ->withType(ilVedaCourseType::SIFA)
                    ->withModified(time())
                    ->withObjID($target->getId())
                    ->withStatusCreated(ilVedaCourseStatus::PENDING)
                    ->store();

                $this->logger->debug('Update title');
                $target->setTitle($tc[self::CP_INFO_NAME]);
                $target->setOfflineStatus(true);
                $target->update();
                $this->createDefaultCourseRole($target, $this->settings->getPermanentSwitchRole(), $train);
                $this->createDefaultCourseRole($target, $this->settings->getTemporarySwitchRole(), $train);

                // delete connection user from administrator role
                $this->deleteAdministratorAssignments($target);
            }
        }
        if ($source instanceof ilObjGroup) {
            $target = ilObjectFactory::getInstanceByRefId($a_target_id, false);
            if ($target instanceof ilObjGroup) {
                // delete connection user from administrator role
                $this->deleteAdministratorAssignments($target);
            }
        }
        if ($source instanceof ilObject) {
            $this->migrateTrainingCourseSegmentToTrain(
                $a_source_id,
                $a_target_id,
                $train,
                $tc[self::CP_INFO_AUSBILDUNGSGANG]
            );
        }
        if ($source instanceof ilObjSession) {
            $this->migrateSessionAppointments($a_target_id, $train);
        }
        if ($source instanceof ilObjExercise) {
            $this->migrateExerciseAppointments($a_target_id, $train);
        }
    }

    protected function migrateTrainingCourseToTrain(int $source_id, int $target_id, Ausbildungszug $train) : void
    {
        $this->md_db_manager->deleteTrainingCourseId($target_id);
        $this->md_db_manager->deleteTrainingCourseTrainId($target_id);

        $tc_oid = $train->getOid();
        $this->md_db_manager->writeTrainingCourseTrainId($target_id, $tc_oid);
        $this->crs_builder_factory->buildCourse()
            ->withOID($train->getOid())
            ->withType(ilVedaCourseType::SIFA)
            ->withModified(time())
            ->withStatusCreated(ilVedaCourseStatus::PENDING)
            ->store();
    }

    protected function migrateTrainingCourseSegmentToTrain(
        int $source_id,
        int $target_id,
        Ausbildungszug $train,
        ?string $training_course_id
    ) : void {
        $this->md_db_manager->deleteTrainingCourseSegmentId($target_id);
        $this->md_db_manager->deleteTrainingCourseSegmentTrainId($target_id);

        $course_segment_id = $this->md_db_manager->lookupSegmentId($source_id);
        $segment_train_id = '';
        foreach ($train->getAusbildungszugabschnitte() as $abschnitt) {
            if (ilVedaUtils::compareOidsEqual($abschnitt->getAusbildungsgangabschnittId(), $course_segment_id)) {
                $segment_train_id = $abschnitt->getOid();
                $this->md_db_manager->writeTrainingCourseSegmentTrainId($target_id, $abschnitt->getOid());
                break;
            }
        }

        try {
            $training_course = $this->veda_connector->getTrainingCourseApi()->getCourse($training_course_id);
            foreach ($training_course->getAusbildungsgangabschnitte() as $training_course_segment) {
                if (ilVedaUtils::compareOidsEqual($training_course_segment->getOid(), $course_segment_id)) {
                    $segment_status = $this->segment_repo->createEmptySegment($segment_train_id);
                    $segment_status->setType($training_course_segment->getAusbildungsgangabschnittsart());
                    $this->segment_repo->updateSegmentInfo($segment_status);
                }
            }
        } catch (Exception $e) {
            $this->mail_segment_builder_factory->buildSegment()
                ->withMessage('Update of training course failed.')
                ->withType(ilVedaMailSegmentType::ERROR)
                ->store();
            $this->logger->error('Update of getAusbildungsgangabschnittsart failed with message: ' . $e->getMessage());
        }
    }

    protected function createDefaultCourseRole(ilObjCourse $course, int $rolt_id, Ausbildungszug $train) : ilObjRole
    {
        $role = new ilObjRole();
        $role->setTitle(ilObject::_lookupTitle($rolt_id));
        $role->create();

        $this->logger->debug('Created new local role');

        $this->rbac_admin->assignRoleToFolder($role->getId(), $course->getRefId(), 'y');
        $this->rbac_admin->copyRoleTemplatePermissions(
            $rolt_id,
            ROLE_FOLDER_ID,
            $course->getRefId(),
            $role->getId()
        );

        $ops = $this->rbac_review->getOperationsOfRole(
            $role->getId(),
            ilObject::_lookupType($course->getRefId(), true),
            $course->getRefId()
        );
        $this->rbac_admin->grantPermission(
            $role->getId(),
            $ops,
            $course->getRefId()
        );

        switch ($rolt_id) {
            case $this->settings->getTemporarySwitchRole():
                $this->crs_builder_factory->buildCourse()
                    ->withOID($train->getOid())
                    ->withType(ilVedaCourseType::SIFA)
                    ->withModified(time())
                    ->withSwithTemporaryRole($role->getId())
                    ->withStatusCreated(ilVedaCourseStatus::PENDING)
                    ->store();
                break;
            case $this->settings->getPermanentSwitchRole():
                $this->crs_builder_factory->buildCourse()
                    ->withOID($train->getOid())
                    ->withType(ilVedaCourseType::SIFA)
                    ->withModified(time())
                    ->withSwitchPermanentRole($role->getId())
                    ->withStatusCreated(ilVedaCourseStatus::PENDING)
                    ->store();
                break;

            default:
                $this->logger->error('Invalid role id given: ' . $rolt_id);
        }
        return $role;
    }

    protected function deleteAdministratorAssignments(ilObject $target) : void
    {
        $participants = ilParticipants::getInstance($target->getRefId());
        foreach ($participants->getAdmins() as $admin_id) {
            $participants->delete($admin_id);
        }
    }

    protected function migrateSessionAppointments(int $target_id, Ausbildungszug $train) : void
    {
        $session = ilObjectFactory::getInstanceByRefId($target_id, false);
        if (!$session instanceof ilObjSession) {
            $this->logger->error('Cannot initiate session with id: ' . $target_id);
            return;
        }
        $app = $session->getFirstAppointment();

        $segment_id = $this->md_db_manager->findTrainSegmentId($session->getRefId());

        if (!$segment_id) {
            $this->logger->debug('No md mapping found for target_id: ' . $target_id);
            return;
        }

        foreach ($train->getAusbildungszugabschnitte() as $train_segment) {
            $segment_begin = null;
            $segment_end = null;
            if (ilVedaUtils::compareOidsEqual($train_segment->getOid(), $segment_id)) {
                $segment_begin = $train_segment->getBeginn();
                $segment_end = $train_segment->getEnde();
            }
            if ($segment_begin instanceof DateTime) {
                $this->logger->debug('Update starting time of session');
                $app->setStart(new ilDateTime($segment_begin->getTimestamp(), IL_CAL_UNIX));
            }
            if ($segment_end instanceof DateTime) {
                $this->logger->debug('Update ending time of session');
                $app->setEnd(new ilDateTime($segment_end->getTimestamp(), IL_CAL_UNIX));
            }
            $app->update();
        }
    }

    protected function migrateExerciseAppointments(int $target_id, Ausbildungszug $train) : void
    {
        $exercise = ilObjectFactory::getInstanceByRefId($target_id, false);
        if (!$exercise instanceof ilObjExercise) {
            $this->logger->error('Cannot initiate exercise with id: ' . $target_id);
            return;
        }

        $segment_id = $this->md_db_manager->findTrainSegmentId($exercise->getRefId());

        if (!$segment_id) {
            $this->logger->debug('No md mapping found for target_id: ' . $target_id);
            return;
        }

        $segment_start = $segment_end = null;
        foreach ($train->getAusbildungszugabschnitte() as $train_segment) {
            $segment_start = $segment_end = null;
            if (ilVedaUtils::compareOidsEqual($train_segment->getOid(), $segment_id)) {
                $segment_start = $train_segment->getBeginn();
                $segment_end = $train_segment->getBearbeitungsende();
                if (!$segment_end instanceof DateTime) {
                    $segment_end = $train_segment->getEnde();
                }
            }
            /*
            if ($segment_start instanceof DateTime) {
                $this->logger->debug('Update starting time of exercise');
                foreach (ilExAssignment::getInstancesByExercise($exercise->getId()) as $assignment) {
                    //$assignment->setStartTime($segment_start->getTimestamp());
                    //$assignment->update();
                }
            }
            */
            if ($segment_end instanceof DateTime) {
                $this->logger->debug('Update deadline  of exercise');
                foreach (ilExAssignment::getInstancesByExercise($exercise->getId()) as $assignment) {
                    if ($assignment->getDeadlineMode() == ilExAssignment::DEADLINE_RELATIVE) {
                        $assignment->setRelDeadlineLastSubmission($segment_end->getTimestamp());
                    } else {
                        $assignment->setDeadline($segment_end->getTimestamp());
                    }
                    $assignment->update();
                }
            }
        }
    }
}