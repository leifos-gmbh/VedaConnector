<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

use ILIAS\Refinery\Factory as RefineryFactory;

/**
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 * @ilCtrl_Calls ilVedaConnectorConfigGUI: ilPropertyFormGUI
 */
class ilVedaConnectorConfigGUI extends ilPluginConfigGUI
{
    protected const TAB_SETTINGS = 'settings';
    protected const TAB_CREDENTIALS = 'credentials';
    protected const TAB_IMPORT = 'import';

    protected const SUBTAB_IMPORT = 'import';
    protected const SUBTAB_IMPORT_USR = 'import_usr';
    protected const SUBTAB_IMPORT_CRS = 'import_crs';

    private ?ilLogger $logger = null;
    private ilToolbarGUI $toolbar;
    private ilLanguage $lng;
    private ilCtrl $ctrl;
    private ilTabsGUI $il_tabs;
    private ilGlobalPageTemplate $tpl;
    private ilRbacReview $il_rbac_review;
    protected RefineryFactory $refinery;
    protected \ILIAS\DI\HTTPServices $http;
    protected ilVedaApiInterface $veda_api;

    /**
     * \ilVedaConnectorConfigGUI constructor.
     */
    public function __construct()
    {
        global $DIC;
        $this->logger = $DIC->logger()->vedaimp();
        $this->toolbar = $DIC->toolbar();
        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->il_tabs = $DIC->tabs();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->il_rbac_review = $DIC->rbac()->review();
        $this->http = $DIC->http();
        $this->refinery = $DIC->refinery();
        $this->veda_api = (new ilVedaApiFactory())->getVedaClientApi();
    }

    /**
     * Forward to property form gui
     */
    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass();
        $this->logger->info($next_class);

        if (strtolower($next_class) === strtolower(\ilPropertyFormGUI::class)) {
            $form = $this->initConfigurationForm();
            $this->ctrl->forwardCommand($form);
        }

        parent::executeCommand();
    }

    /**
     * @inheritdoc
     */
    public function performCommand($cmd)
    {
        $this->il_tabs->addTab(
            self::TAB_CREDENTIALS,
            ilVedaConnectorPlugin::getInstance()->txt('tab_credentials'),
            $this->ctrl->getLinkTarget($this, 'credentials')
        );
        $this->il_tabs->addTab(
            self::TAB_SETTINGS,
            ilVedaConnectorPlugin::getInstance()->txt('tab_settings'),
            $this->ctrl->getLinkTarget($this, 'configure')
        );
        $this->il_tabs->addTab(
            self::TAB_IMPORT,
            ilVedaConnectorPlugin::getInstance()->txt('tab_import'),
            $this->ctrl->getLinkTarget($this, 'import')
        );

        $this->$cmd();
    }

    /**
     * @inheritdoc
     */
    protected function configure(ilPropertyFormGUI $form = null) : void
    {
        $this->il_tabs->activateTab(self::TAB_SETTINGS);
        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initConfigurationForm();
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @return \ilPropertyFormGUI
     */
    protected function initConfigurationForm() : ilPropertyFormGUI
    {
        $settings = ilVedaConnectorSettings::getInstance();

        $form = new \ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_settings'));
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->addCommandButton('save', $this->lng->txt('save'));
        $form->setShowTopButtons(false);

        $lock = new \ilCheckboxInputGUI($this->getPluginObject()->txt('tbl_veda_settings_active'), 'active');
        $lock->setValue(1);
        $lock->setChecked($settings->isActive());
        $form->addItem($lock);

        $lock = new ilCheckboxInputGUI($this->getPluginObject()->txt('tbl_veda_settings_lock'), 'lock');
        $lock->setValue(1);
        $lock->setDisabled(!$settings->isLocked());
        $lock->setChecked($settings->isLocked());
        $lock->setInfo($this->getPluginObject()->txt('tbl_veda_settings_lock_info'));
        $form->addItem($lock);

        $this->lng->loadLanguageModule('log');
        $level = new ilSelectInputGUI($this->getPluginObject()->txt('tbl_veda_settings_loglevel'), 'log_level');
        $level->setHideSubForm($settings->getLogLevel() == \ilLogLevel::OFF, '< 1000');
        $level->setOptions(\ilLogLevel::getLevelOptions());
        $level->setValue($settings->getLogLevel());
        $form->addItem($level);

        $log_file = new \ilTextInputGUI($this->getPluginObject()->txt('tbl_veda_settings_logfile'), 'log_file');
        $log_file->setValue($settings->getLogFile());
        $log_file->setInfo($this->getPluginObject()->txt('tbl_veda_settings_logfile_info'));
        $level->addSubItem($log_file);

        // cron interval
        $cron_i = new ilNumberInputGUI($this->getPluginObject()->txt('cron'), 'cron_interval');
        $cron_i->setMinValue(1);
        $cron_i->setSize(2);
        $cron_i->setMaxLength(3);
        $cron_i->setRequired(true);
        $cron_i->setValue($settings->getCronInterval());
        $cron_i->setInfo($this->getPluginObject()->txt('cron_interval'));

        $mail_settings_header = new ilFormSectionHeaderGUI();
        $mail_settings_header->setTitle($this->getPluginObject()->txt('tbl_settings_section_mail'));
        $form->addItem($mail_settings_header);

        $mail_active = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_mail_active'),
            'mail_active'
        );
        $mail_active->setChecked($settings->isMailActive());
        $form->addItem($mail_active);

        $mail_targets = new ilTextInputGUI($this->getPluginObject()->txt('tbl_mail_targets'), 'mail_targets');
        $mail_targets->setValue($settings->getMailTargets());
        $mail_targets->setInfo($this->getPluginObject()->txt('tbl_mail_targets_info'));
        $mail_targets->setRequired(true);
        $mail_active->addSubItem($mail_targets);

        $sifa_sync = new \ilFormSectionHeaderGUI();
        $sifa_sync->setTitle($this->getPluginObject()->txt('tbl_settings_section_sifa_sync'));
        $form->addItem($sifa_sync);

        $sifa_active = new \ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_sifa_active'),
            'sifa_active'
        );
        $sifa_active->setChecked($settings->isSifaActive());
        $form->addItem($sifa_active);

        $roles = new ilSelectInputGUI(
            $this->getPluginObject()->txt('tbl_settings_participant_role'),
            'sifa_participant_role'
        );
        $roles->setValue($settings->getSifaParticipantRole());
        $roles->setInfo($this->getPluginObject()->txt('tbl_settings_participant_role_info'));
        $roles->setOptions($this->prepareRoleSelection());
        $roles->setRequired(true);
        $sifa_active->addSubItem($roles);

        $import_dir = new \ilRepositorySelector2InputGUI(
            $this->getPluginObject()->txt('tbl_settings_course_import'),
            'sifa_crs_import',
            true
        );
        $import_dir->setRequired(true);
        $import_dir->setInfo($this->getPluginObject()->txt('tbl_settings_course_import_info'));
        $white_list[] = 'cat';
        $import_dir->getExplorerGUI()->setTypeWhiteList($white_list);
        $import_dir->setValue($settings->getSifaImportDirectory());
        $sifa_active->addSubItem($import_dir);

        $switch = new \ilNumberInputGUI(
            $this->getPluginObject()->txt('tbl_settings_switch_permanent_role'),
            'switch_permanent'
        );
        $switch->setRequired(true);
        if ($settings->getPermanentSwitchRole()) {
            $switch->setValue($settings->getPermanentSwitchRole());
            $switch->setSuffix(\ilObject::_lookupTitle($settings->getPermanentSwitchRole()));
        }
        $switch->setInfo($this->getPluginObject()->txt('tbl_settings_switch_permanent_role_info'));
        $sifa_active->addSubItem($switch);

        $switcht = new \ilNumberInputGUI(
            $this->getPluginObject()->txt('tbl_settings_switch_temp_role'),
            'switch_temp'
        );
        $switcht->setRequired(true);
        if ($settings->getTemporarySwitchRole()) {
            $switcht->setValue($settings->getTemporarySwitchRole());
            $switcht->setSuffix(\ilObject::_lookupTitle($settings->getTemporarySwitchRole()));
        }
        $switcht->setInfo($this->getPluginObject()->txt('tbl_settings_switch_temp_role_info'));
        $sifa_active->addSubItem($switcht);

        $standard_sync = new \ilFormSectionHeaderGUI();
        $standard_sync->setTitle($this->getPluginObject()->txt('tbl_settings_section_standard_sync'));
        $form->addItem($standard_sync);

        $standard_active = new \ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_standard_active'),
            'standard_active'
        );
        $standard_active->setChecked($settings->isStandardActive());
        $form->addItem($standard_active);

        $roles = new ilSelectInputGUI(
            $this->getPluginObject()->txt('tbl_settings_participant_role'),
            'standard_participant_role'
        );
        $roles->setValue($settings->getStandardParticipantRole());
        $roles->setInfo($this->getPluginObject()->txt('tbl_settings_participant_role_info'));
        $roles->setOptions($this->prepareRoleSelection());
        $roles->setRequired(true);
        $standard_active->addSubItem($roles);

        $import_dir = new \ilRepositorySelector2InputGUI(
            $this->getPluginObject()->txt('tbl_settings_course_import'),
            'standard_crs_import',
            true
        );
        $import_dir->setRequired(true);
        $import_dir->setInfo($this->getPluginObject()->txt('tbl_settings_course_import_info'));
        $white_list[] = 'cat';
        $import_dir->getExplorerGUI()->setTypeWhiteList($white_list);
        $import_dir->setValue($settings->getStandardImportDirectory());
        $standard_active->addSubItem($import_dir);

        return $form;
    }

    protected function save() : void
    {
        $form = $this->initConfigurationForm();
        $settings = ilVedaConnectorSettings::getInstance();

        try {
            if ($form->checkInput()) {
                $settings->setActive($form->getInput('active'));
                $settings->setLogLevel($form->getInput('log_level'));
                $settings->setLogFile($form->getInput('log_file'));
                $settings->setSifaParticipantRole($form->getInput('sifa_participant_role'));
                $settings->setStandardParticipantRole($form->getInput('standard_participant_role'));
                $settings->enableLock($form->getInput('lock'));

                $category_ref_ids = $form->getInput('sifa_crs_import');
                $settings->setSifaImportDirectory((int) end($category_ref_ids));
                $category_ref_ids = $form->getInput('standard_crs_import');
                $settings->setStandardImportDirectory((int) end($category_ref_ids));
                $settings->setPermanentSwitchRole((int) $form->getInput('switch_permanent'));
                $settings->setTemporarySwitchRole((int) $form->getInput('switch_temp'));

                $settings->setStandardActive($form->getInput('standard_active'));
                $settings->setSiFaActive($form->getInput('sifa_active'));

                $settings->setMailActive($form->getInput('mail_active'));
                $settings->setMailTargets($form->getInput('mail_targets'));

                ilUtil::sendSuccess($this->lng->txt('settings_saved'), true);
                $this->ctrl->redirect($this, 'configure');
            }
            $error = $this->lng->txt('err_check_input');
        } catch (ilException $e) {
            $error = $e->getMessage();
            $this->logger->error('Configuration error: ' . $error);
        }
        $form->setValuesByPost();
        ilUtil::sendFailure($error);
        $this->configure($form);
    }

    protected function credentials(?ilPropertyFormGUI $form = null) : void
    {
        $this->il_tabs->activateTab(self::TAB_CREDENTIALS);

        if (ilVedaConnectorSettings::getInstance()->hasSettingsForConnectionTest()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->getPluginObject()->txt('connection_test'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'testConnection'));
            $this->toolbar->addButtonInstance($button);
        }

        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initCredentialsForm();
        }

        $this->tpl->setContent($form->getHTML());
    }

    protected function initCredentialsForm() : ilPropertyFormGUI
    {
        $settings = ilVedaConnectorSettings::getInstance();

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_settings'));
        $form->setFormAction($this->ctrl->getFormAction($this));

        $form->addCommandButton('saveCredentials', $this->lng->txt('save'));
        $form->setShowTopButtons(false);

        $url = new ilTextInputGUI($this->getPluginObject()->txt('credentials_url'), 'resturl');
        $url->setRequired(true);
        $url->setSize(120);
        $url->setMaxLength(512);
        $url->setValue($settings->getRestUrl());
        $form->addItem($url);

        $authentication_id = new ilTextInputGUI($this->getPluginObject()->txt('authentication_id'), 'authentication_id');
        $authentication_id->setRequired(true);
        $authentication_id->setValue($settings->getAuthenticationToken());
        $authentication_id->setInfo($this->getPluginObject()->txt('authentication_id_info'));
        $form->addItem($authentication_id);

        $platform_id = new ilTextInputGUI($this->getPluginObject()->txt('platform_id'), 'platform_id');
        $platform_id->setRequired(true);
        $platform_id->setValue($settings->getPlatformId());
        $platform_id->setInfo($this->getPluginObject()->txt('platform_id_info'));
        $form->addItem($platform_id);

        $add_header_auth = new ilCheckboxInputGUI($this->getPluginObject()->txt('additional_header_authentication'), 'add_header_auth');
        $add_header_auth->setChecked($settings->isAddHeaderAuthEnabled());

        $add_header_name = new ilTextInputGUI($this->getPluginObject()->txt('additional_header_name'), 'add_header_name');
        $add_header_name->setValue($settings->getAddHeaderName());

        $add_header_auth->addSubItem($add_header_name);

        $add_header_value = new ilTextInputGUI($this->getPluginObject()->txt('additional_header_value'), 'add_header_value');
        $add_header_value->setValue($settings->getAddHeaderValue());

        $add_header_auth->addSubItem($add_header_value);

        $form->addItem($add_header_auth);

        return $form;
    }

    protected function saveCredentials() : void
    {
        $form = $this->initCredentialsForm();
        $settings = ilVedaConnectorSettings::getInstance();

        try {
            if ($form->checkInput()) {
                $settings->setRestUrl($form->getInput('resturl'));
                $settings->setRestUser($form->getInput('restuser'));
                $settings->setRestPassword($form->getInput('restpassword'));
                $settings->setAuthenticationToken($form->getInput('authentication_id'));
                $settings->setPlatformId($form->getInput('platform_id'));
                $settings->setAddHeaderAuth((bool) $form->getInput('add_header_auth'));
                $settings->setAddHeaderName($form->getInput('add_header_name'));
                $settings->setAddHeaderValue($form->getInput('add_header_value'));

                ilUtil::sendSuccess($this->lng->txt('settings_saved'), true);
                $this->ctrl->redirect($this, 'credentials');
            }
            $error = $this->lng->txt('err_check_input');
        } catch (ilException $e) {
            $error = $e->getMessage();
            ilVedaConnectorPlugin::getInstance()->getLogger()->error('Error saving credentials: ' . $error);
        }
        $form->setValuesByPost();
        ilUtil::sendFailure($error);
        $this->credentials($form);
    }

    protected function import(?ilPropertyFormGUI $form = null) : void
    {
        $this->setSubTabs();
        $this->il_tabs->activateTab(self::TAB_IMPORT);
        $this->il_tabs->activateSubTab(self::SUBTAB_IMPORT);

        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initImportForm();
        }
        $this->tpl->setContent($form->getHTML());
    }

    protected function initImportForm() : ilPropertyFormGUI
    {
        $form = new ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_import'));
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->addCommandButton('doImport', $this->getPluginObject()->txt('btn_import'));

        $sifa = new ilFormSectionHeaderGUI();
        $sifa->setTitle($this->getPluginObject()->txt('section_import_sifa'));
        $form->addItem($sifa);

        // selection all or single elements
        $imp_type = new ilRadioGroupInputGUI($this->getPluginObject()->txt('import_selection'), 'selection_' . ilVedaImporter::IMPORT_TYPE_SIFA);
        $imp_type->setValue(ilVedaImporter::IMPORT_NONE);
        $form->addItem($imp_type);

        $none = new ilRadioOption($this->getPluginObject()->txt('import_selection_none'), ilVedaImporter::IMPORT_NONE);
        $imp_type->addOption($none);

        $all = new ilRadioOption($this->getPluginObject()->txt('import_selection_all'), ilVedaImporter::IMPORT_ALL);
        $imp_type->addOption($all);

        $sel = new ilRadioOption($this->getPluginObject()->txt('import_selection_selected'), ilVedaImporter::IMPORT_SELECTED);
        $imp_type->addOption($sel);

        $usr = new ilCheckboxInputGUI($this->lng->txt('obj_usr'), 'usr_' . ilVedaImporter::IMPORT_TYPE_SIFA);
        $usr->setValue(ilVedaImporter::IMPORT_USR);
        $sel->addSubItem($usr);


        $crs = new ilCheckboxInputGUI($this->lng->txt('objs_crs'), 'crs_' . ilVedaImporter::IMPORT_TYPE_SIFA);
        $crs->setValue(ilVedaImporter::IMPORT_CRS);
        $sel->addSubItem($crs);

        $mem = new ilCheckboxInputGUI($this->getPluginObject()->txt('type_membership'), 'mem_' . ilVedaImporter::IMPORT_TYPE_SIFA);
        $mem->setValue(ilVedaImporter::IMPORT_MEM);
        $sel->addSubItem($mem);

        $standard = new ilFormSectionHeaderGUI();
        $standard->setTitle($this->getPluginObject()->txt('section_import_standard'));
        $form->addItem($standard);

        // selection all or single elements
        $imp_type = new ilRadioGroupInputGUI($this->getPluginObject()->txt('import_selection'), 'selection_' . ilVedaImporter::IMPORT_TYPE_STANDARD);
        $imp_type->setValue(ilVedaImporter::IMPORT_NONE);
        $form->addItem($imp_type);

        $none = new ilRadioOption($this->getPluginObject()->txt('import_selection_none'), ilVedaImporter::IMPORT_NONE);
        $imp_type->addOption($none);

        $all = new ilRadioOption($this->getPluginObject()->txt('import_selection_all'), ilVedaImporter::IMPORT_ALL);
        $imp_type->addOption($all);

        $sel = new ilRadioOption($this->getPluginObject()->txt('import_selection_selected'), ilVedaImporter::IMPORT_SELECTED);
        $imp_type->addOption($sel);

        $usr = new ilCheckboxInputGUI($this->lng->txt('obj_usr'), 'usr_' . ilVedaImporter::IMPORT_TYPE_STANDARD);
        $usr->setValue(ilVedaImporter::IMPORT_USR);
        $sel->addSubItem($usr);


        $crs = new ilCheckboxInputGUI($this->lng->txt('objs_crs'), 'crs_' . ilVedaImporter::IMPORT_TYPE_STANDARD);
        $crs->setValue(ilVedaImporter::IMPORT_CRS);
        $sel->addSubItem($crs);

        $mem = new ilCheckboxInputGUI($this->getPluginObject()->txt('type_membership'), 'mem_' . ilVedaImporter::IMPORT_TYPE_STANDARD);
        $mem->setValue(ilVedaImporter::IMPORT_MEM);
        $sel->addSubItem($mem);

        $form->setShowTopButtons(false);

        return $form;
    }

    protected function doImport() : void
    {
        $form = $this->initImportForm();
        if (!$form->checkInput()) {
            ilUtil::sendFailure($this->lng->txt('err_check_input'));
            $this->import($form);
        }

        try {
            foreach ([ilVedaImporter::IMPORT_TYPE_SIFA, ilVedaImporter::IMPORT_TYPE_STANDARD] as $import_type) {
                $import_type_selection = (int) $form->getInput('selection_' . ((string) $import_type));
                if ($import_type_selection == ilVedaImporter::IMPORT_NONE) {
                    continue;
                }
                $modes = [];
                foreach ([
                    ilVedaImporter::IMPORT_USR,
                    ilVedaImporter::IMPORT_CRS,
                    ilVedaImporter::IMPORT_MEM] as $mode) {
                    if ($form->getInput($mode . '_' . ((string) $import_type))) {
                        $modes[] = $mode;
                    }
                }
                $importer = ilVedaImporter::getInstance();
                $this->logger->dump($import_type, ilLogLevel::NOTICE);
                $this->logger->dump($import_type_selection, ilLogLevel::NOTICE);
                $this->logger->dump($modes, ilLogLevel::NOTICE);
                $importer->import(
                    $import_type,
                    ($import_type_selection === ilVedaImporter::IMPORT_ALL),
                    $modes
                );
            }
        } catch (Exception $e) {
            $this->logger->logStack(ilLogLevel::WARNING);
            ilUtil::sendFailure('Import failed with message: ' . $e->getMessage());
            $this->import($form);
        }

        ilUtil::sendSuccess($this->getPluginObject()->txt('success_import'));
        $form->setValuesByPost();
        $this->import($form);
    }

    /**
     * show user import result
     */
    protected function importResultUser() : void
    {
        $this->setSubTabs();
        $this->il_tabs->activateTab(self::TAB_IMPORT);
        $this->il_tabs->activateSubTab(self::SUBTAB_IMPORT_USR);
        $table = new ilVedaUserImportResultTableGUI($this, __FUNCTION__);
        $table->init();
        $table->parse();
        $this->tpl->setContent($table->getHTML());
    }

    /**
     * @throws \ilDatabaseException
     */
    protected function importResultCourse()
    {
        $this->setSubTabs();
        $this->il_tabs->activateTab(self::TAB_IMPORT);
        $this->il_tabs->activateSubTab(self::SUBTAB_IMPORT_CRS);
        $table = new ilVedaCourseImportResultTableGUI($this, __FUNCTION__);
        $table->init();
        $table->parse();
        $this->tpl->setContent($table->getHTML());
        (new ilVedaMailManager())->sendStatus();
    }

    protected function setSubTabs()
    {
        $this->il_tabs->addSubTab(
            self::SUBTAB_IMPORT,
            $this->getPluginObject()->txt('subtab_import'),
            $this->ctrl->getLinkTarget($this, 'import')
        );
        $this->il_tabs->addSubTab(
            self::SUBTAB_IMPORT_USR,
            $this->getPluginObject()->txt('subtab_import_usr'),
            $this->ctrl->getLinkTarget($this, 'importResultUser')
        );
        $this->il_tabs->addSubTab(
            self::SUBTAB_IMPORT_CRS,
            $this->getPluginObject()->txt('subtab_import_crs'),
            $this->ctrl->getLinkTarget($this, 'importResultCourse')
        );
    }

    protected function testConnection()
    {
        if ($this->veda_api->testConnection()) {
            ilUtil::sendSuccess($this->getPluginObject()->txt('success_api_connect'));
        } else {
            ilUtil::sendFailure('API Connection Failed');
        }
        $this->credentials();
    }

    /**
     * @return mixed
     */
    protected function prepareRoleSelection(bool $a_with_select_option = true) : array
    {
        $global_roles = ilUtil::_sortIds(
            $this->il_rbac_review->getGlobalRoles(),
            'object_data',
            'title',
            'obj_id'
        );
        $select = [];
        if ($a_with_select_option) {
            $select[0] = $this->lng->txt('links_select_one');
        }
        foreach ($global_roles as $role_id) {
            if ($role_id == ANONYMOUS_ROLE_ID) {
                continue;
            }
            $select[$role_id] = ilObject::_lookupTitle($role_id);
        }
        return $select;
    }

    protected function migrateUser()
    {
        $oid = $this->http->request()->getQueryParams()['oid'] ?? '';
        $login = urldecode($this->http->request()->getQueryParams()['login'] ?? '');
        if ($oid === '' || $login === '') {
            ilUtil::sendFailure($this->lng->txt('err_check_input'), true);
            $this->ctrl->redirect($this, 'importResultUser');
        }
        $obj_id_from_oid = ilObjUser::_getImportedUserId($oid);
        $type_from_oid = ilObject::_lookupType($obj_id_from_oid);
        $obj_id_from_login = ilObjUser::_loginExists($login);
        $import_id_from_login = ilObject::_lookupImportId($obj_id_from_login);

        if ($import_id_from_login != '') {
            $this->logger->warning('Migration failed: user already imported');
            ilUtil::sendFailure($this->lng->txt('err_check_input'), true);
            $this->ctrl->redirect($this, 'importResultUser');
        }
        if (!$obj_id_from_login) {
            $this->logger->warning('Migration failed: user does not exist');
            ilUtil::sendFailure($this->lng->txt('err_check_input'), true);
            $this->ctrl->redirect($this, 'importResultUser');
        }
        if ($obj_id_from_oid > 0) {
            $this->logger->warning('Migration failed: user already imported');
            ilUtil::sendFailure($this->lng->txt('err_check_input'), true);
            $this->ctrl->redirect($this, 'importResultUser');
        }
        ilObjUser::_writeImportId($obj_id_from_login, $oid);

        $user_repo = (new ilVedaRepositoryFactory())->getUserRepository();
        $status = $user_repo->lookupUserByOID($oid);
        $status = is_null($status) ? $user_repo->createEmptyUser($oid) : $status;
        $status->setImportFailure(false);
        $user_repo->updateUser($status);

        ilUtil::sendSuccess(ilVedaConnectorPlugin::getInstance()->txt('migrated_account'), true);
        $this->ctrl->redirect($this, 'importResultUser');
    }
}
