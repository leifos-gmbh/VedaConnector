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

declare(strict_types=1);

use ILIAS\HTTP\Services;
use ILIAS\Refinery\Factory as RefineryFactory;
use Leifos\VedaConnector\Factory as VedaFactory;
use Leifos\VedaConnector\I\FactoryInterface as VedaFactoryInterface;
use Leifos\VedaConnector\I\ImporterInterface as ImporterInterface;
use Leifos\VedaConnector\I\Settings\Name;
use Leifos\VedaConnector\InputFields\RefIdNumber;

/**
 * @ilCtrl_isCalledBy ilVedaConnectorConfigGUI: ilObjComponentSettingsGUI
 */
class ilVedaConnectorConfigGUI extends ilPluginConfigGUI
{
    protected const TAB_SETTINGS = 'settings';
    protected const TAB_CREDENTIALS = 'credentials';
    protected const TAB_IMPORT = 'import';

    protected const SUBTAB_IMPORT = 'import';
    protected const SUBTAB_IMPORT_USR = 'import_usr';
    protected const SUBTAB_IMPORT_CRS = 'import_crs';

    private ilToolbarGUI $toolbar;
    private ilLanguage $lng;
    private ilCtrl $ctrl;
    private ilTabsGUI $il_tabs;
    private ilGlobalTemplateInterface $tpl;
    private ilRbacReview $il_rbac_review;
    protected RefineryFactory $refinery;
    protected Services $http;
    protected VedaFactoryInterface $veda_factory;

    public function __construct()
    {
        global $DIC;
        $this->veda_factory = VedaFactory::getInstance();
        $this->toolbar = $DIC->toolbar();
        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->il_tabs = $DIC->tabs();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->il_rbac_review = $DIC->rbac()->review();
        $this->http = $DIC->http();
        $this->refinery = $DIC->refinery();
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass();
        $this->veda_factory->logger()->handler()->info($next_class);
        if (strtolower($next_class) === strtolower(ilPropertyFormGUI::class)) {
            $form = $this->initConfigurationForm();
            $this->ctrl->forwardCommand($form);
        }
        parent::executeCommand();
    }

    /**
     * @throws ilCtrlException
     */
    public function performCommand(string $cmd): void
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
     * @throws ilCtrlException
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
     * @throws ilCtrlException
     */
    protected function initConfigurationForm() : ilPropertyFormGUI
    {
        $settings = $this->veda_factory->settings()->handler();

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_settings'));
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->addCommandButton('save', $this->lng->txt('save'));
        $form->setShowTopButtons(false);

        $lock = new ilCheckboxInputGUI($this->getPluginObject()->txt('tbl_veda_settings_active'), 'active');
        $lock->setValue('1');
        $lock->setChecked($settings->readAsBool(Name::ACTIVE));
        $form->addItem($lock);

        $lock = new ilCheckboxInputGUI($this->getPluginObject()->txt('tbl_veda_settings_lock'), 'lock');
        $lock->setValue('1');
        $lock->setDisabled(!$settings->readAsBool(Name::LOCK));
        $lock->setChecked($settings->readAsBool(Name::LOCK));
        $lock->setInfo($this->getPluginObject()->txt('tbl_veda_settings_lock_info'));
        $form->addItem($lock);

        $this->lng->loadLanguageModule('log');
        $level = new ilSelectInputGUI($this->getPluginObject()->txt('tbl_veda_settings_loglevel'), 'log_level');
        $level->setHideSubForm($settings->readAsInt(Name::LOGLEVEL) == ilLogLevel::OFF, '< 1000');
        $level->setOptions(ilLogLevel::getLevelOptions());
        $level->setValue($settings->readAsInt(Name::LOGLEVEL));
        $form->addItem($level);

        $log_file = new ilTextInputGUI($this->getPluginObject()->txt('tbl_veda_settings_logfile'), 'log_file');
        $log_file->setValue($settings->read(Name::LOGFILE));
        $log_file->setInfo($this->getPluginObject()->txt('tbl_veda_settings_logfile_info'));
        $level->addSubItem($log_file);

        // cron interval
        $cron_i = new ilNumberInputGUI($this->getPluginObject()->txt('cron'), 'cron_interval');
        $cron_i->setMinValue(1);
        $cron_i->setSize(2);
        $cron_i->setMaxLength(3);
        $cron_i->setRequired(true);
        $cron_i->setValue($settings->read(Name::CRON_INTERVAL));
        $cron_i->setInfo($this->getPluginObject()->txt('cron_interval'));

        $mail_settings_header = new ilFormSectionHeaderGUI();
        $mail_settings_header->setTitle($this->getPluginObject()->txt('tbl_settings_section_mail'));
        $form->addItem($mail_settings_header);

        $mail_active = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_mail_active'),
            'mail_active'
        );
        $mail_active->setChecked($settings->readAsBool(Name::MAIL_ACTIVE));
        $form->addItem($mail_active);

        $mail_targets = new ilTextInputGUI($this->getPluginObject()->txt('tbl_mail_targets'), 'mail_targets');
        $mail_targets->setValue($settings->read(Name::MAIL_TARGETS));
        $mail_targets->setInfo($this->getPluginObject()->txt('tbl_mail_targets_info'));
        $mail_targets->setRequired(true);
        $mail_active->addSubItem($mail_targets);

        $sifa_sync = new ilFormSectionHeaderGUI();
        $sifa_sync->setTitle($this->getPluginObject()->txt('tbl_settings_section_sifa_sync'));
        $form->addItem($sifa_sync);

        $sifa_active = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_sifa_active'),
            'sifa_active'
        );
        $sifa_active->setChecked($settings->readAsBool(Name::SIFA_ACTIVE));
        $form->addItem($sifa_active);

        $roles = new ilSelectInputGUI(
            $this->getPluginObject()->txt('tbl_settings_participant_role'),
            'sifa_participant_role'
        );
        $roles->setValue($settings->readAsInt(Name::PART_ROLE));
        $roles->setInfo($this->getPluginObject()->txt('tbl_settings_participant_role_info'));
        $roles->setOptions($this->prepareRoleSelection());
        $roles->setRequired(true);
        $sifa_active->addSubItem($roles);

        $import_dir = new RefIdNumber(
            $this->veda_factory->plugin(),
            $this->getPluginObject()->txt('tbl_settings_course_import'),
            'sifa_crs_import'
        );
        $import_dir->setRequired(true);
        $import_dir->setInfo($this->getPluginObject()->txt('tbl_settings_course_import_info'));
        $import_dir->setValue($settings->read(Name::SIFA_IMPORT_REF_ID));
        $import_dir->addSubItem($this->buildImportDirectoryInfoElement(
            $settings->readAsInt(Name::SIFA_IMPORT_REF_ID),
            'sifa_import_cat_info'
        ));
        $sifa_active->addSubItem($import_dir);

        $switch = new ilNumberInputGUI(
            $this->getPluginObject()->txt('tbl_settings_switch_permanent_role'),
            'switch_permanent'
        );
        $switch->setRequired(true);
        if ($settings->read(NAME::PERMANENT_SWITCH_ROLE)) {
            $switch->setValue($settings->read(NAME::PERMANENT_SWITCH_ROLE));
            $switch->setSuffix(ilObject::_lookupTitle($settings->readAsInt(NAME::PERMANENT_SWITCH_ROLE)));
        }
        $switch->setInfo($this->getPluginObject()->txt('tbl_settings_switch_permanent_role_info'));
        $sifa_active->addSubItem($switch);

        $switcht = new ilNumberInputGUI(
            $this->getPluginObject()->txt('tbl_settings_switch_temp_role'),
            'switch_temp'
        );
        $switcht->setRequired(true);
        if ($settings->read(NAME::TEMPORARY_SWITCH_ROLE)) {
            $switcht->setValue($settings->read(NAME::TEMPORARY_SWITCH_ROLE));
            $switcht->setSuffix(ilObject::_lookupTitle($settings->readAsInt(NAME::TEMPORARY_SWITCH_ROLE)));
        }
        $switcht->setInfo($this->getPluginObject()->txt('tbl_settings_switch_temp_role_info'));
        $sifa_active->addSubItem($switcht);

        $standard_sync = new ilFormSectionHeaderGUI();
        $standard_sync->setTitle($this->getPluginObject()->txt('tbl_settings_section_standard_sync'));
        $form->addItem($standard_sync);

        $standard_active = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('tbl_settings_standard_active'),
            'standard_active'
        );
        $standard_active->setChecked($settings->readAsBool(Name::STANDARD_ACTIVE));
        $form->addItem($standard_active);

        $roles = new ilSelectInputGUI(
            $this->getPluginObject()->txt('tbl_settings_participant_role'),
            'standard_participant_role'
        );
        $roles->setValue($settings->readAsInt(Name::STANDARD_PART_ROLE));
        $roles->setInfo($this->getPluginObject()->txt('tbl_settings_participant_role_info'));
        $roles->setOptions($this->prepareRoleSelection());
        $roles->setRequired(true);
        $standard_active->addSubItem($roles);

        $import_dir = $this->veda_factory->inputFields()->refIdNumber(
            $this->getPluginObject()->txt('tbl_settings_course_import'),
            'standard_crs_import'
        );
        $import_dir->setRequired(true);
        $import_dir->setInfo($this->getPluginObject()->txt('tbl_settings_course_import_info'));
        $import_dir->setValue($settings->read(Name::STANDARD_IMPORT_REF_ID));
        $standard_active->addSubItem($import_dir);
        $import_dir->addSubItem($this->buildImportDirectoryInfoElement(
            $settings->readAsInt(Name::STANDARD_IMPORT_REF_ID),
            'standard_import_cat_info'
        ));
        return $form;
    }

    protected function buildImportDirectoryInfoElement(int $ref_id, string $post_var): ilTextInputGUI
    {
        $import_dir_name = $this->getImportDirectoryName($ref_id);
        $info_text = new ilTextInputGUI(
            $this->getPluginObject()->txt('tbl_settings_course_import_category_title'),
            $post_var
        );
        $info_text->setValue($import_dir_name ?? '');
        $info_text->setDisabled(true);
        $info_text->setInfo($this->getPluginObject()->txt('tbl_settings_course_import_category_info'));
        return $info_text;
    }

    protected function getImportDirectoryName(int $ref_id): ?string
    {
        $obj_id = ilObject::_lookupObjectId($ref_id);
        return $this->isCategoryObject($ref_id) ? ilObject::_lookupTitle($obj_id) : null;
    }

    protected function isCategoryObject(int $ref_id): bool
    {
        return ilObject::_lookupType($ref_id, true) === 'cat';
    }

    /**
     * @throws ilCtrlException
     */
    protected function save() : void
    {
        $form = $this->initConfigurationForm();
        $settings = $this->veda_factory->settings()->handler();
        try {
            if (
                $form->checkInput()
            ) {
                $settings->writeBool(NAME::ACTIVE, (bool) $form->getInput('active'));
                $settings->writeInt(Name::LOGLEVEL, (int) $form->getInput('log_level'));
                $settings->write(Name::LOGFILE, (string) $form->getInput('log_file'));
                $settings->writeInt(Name::PART_ROLE, (int) $form->getInput('sifa_participant_role'));
                $settings->writeInt(Name::STANDARD_PART_ROLE, (int) $form->getInput('standard_participant_role'));
                $settings->writeBool(Name::LOCK, (bool) $form->getInput('lock'));
                $settings->writeInt(Name::SIFA_IMPORT_REF_ID, (int) $form->getInput('sifa_crs_import'));
                $settings->writeInt(Name::STANDARD_IMPORT_REF_ID, (int) $form->getInput('standard_crs_import'));
                $settings->writeInt(Name::PERMANENT_SWITCH_ROLE, (int) $form->getInput('switch_permanent'));
                $settings->writeInt(Name::TEMPORARY_SWITCH_ROLE, (int) $form->getInput('switch_temp'));
                $settings->writeBool(Name::STANDARD_ACTIVE, (bool) $form->getInput('standard_active'));
                $settings->writeBool(Name::SIFA_ACTIVE, (bool) $form->getInput('sifa_active'));
                $settings->writeBool(Name::MAIL_ACTIVE, (bool) $form->getInput('mail_active'));
                $settings->write(Name::MAIL_TARGETS, (string) $form->getInput('mail_targets'));
                $this->tpl->setOnScreenMessage(
                    ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
                    $this->lng->txt('settings_saved'),
                    true
                );
                $this->ctrl->redirect($this, 'configure');
            }
            $error = $this->lng->txt('err_check_input');
        } catch (ilException $e) {
            $error = $e->getMessage();
            $this->veda_factory->logger()->handler()->error('Configuration error: ' . $error);
        }
        $form->setValuesByPost();
        $this->tpl->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $error);
        $this->configure($form);
    }

    /**
     * @throws ilCtrlException
     */
    protected function credentials(?ilPropertyFormGUI $form = null) : void
    {
        $this->il_tabs->activateTab(self::TAB_CREDENTIALS);
        if ($this->veda_factory->settings()->handler()->hasSettingsForConnectionTest()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->getPluginObject()->txt('connection_test_rest'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'testConnection'));
            $this->toolbar->addButtonInstance($button);
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->getPluginObject()->txt('connection_test_soap'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'testConnectionSoap'));
            $this->toolbar->addButtonInstance($button);
        }
        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initCredentialsForm();
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function initCredentialsForm() : ilPropertyFormGUI
    {
        $settings = $this->veda_factory->settings()->handler();

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_settings'));
        $form->setFormAction($this->ctrl->getFormAction($this));

        $form->addCommandButton('saveCredentials', $this->lng->txt('save'));
        $form->setShowTopButtons(false);

        $url = new ilTextInputGUI($this->getPluginObject()->txt('credentials_url'), 'resturl');
        $url->setRequired(true);
        $url->setSize(120);
        $url->setMaxLength(512);
        $url->setValue($settings->read(Name::REST_URL));
        $form->addItem($url);

        $authentication_id = new ilTextInputGUI($this->getPluginObject()->txt('authentication_id'), 'authentication_id');
        $authentication_id->setRequired(true);
        $authentication_id->setValue($settings->read(Name::REST_TOKEN));
        $authentication_id->setInfo($this->getPluginObject()->txt('authentication_id_info'));
        $form->addItem($authentication_id);

        $platform_id = new ilTextInputGUI($this->getPluginObject()->txt('platform_id'), 'platform_id');
        $platform_id->setRequired(true);
        $platform_id->setValue($settings->read(Name::PLATTFORM_ID));
        $platform_id->setInfo($this->getPluginObject()->txt('platform_id_info'));
        $form->addItem($platform_id);

        $add_header_auth = new ilCheckboxInputGUI($this->getPluginObject()->txt('additional_header_authentication'), 'add_header_auth');
        $add_header_auth->setChecked($settings->readAsBool(Name::ADD_HEADER_AUTH));

        $add_header_name = new ilTextInputGUI($this->getPluginObject()->txt('additional_header_name'), 'add_header_name');
        $add_header_name->setValue($settings->read(Name::ADD_HEADER_NAME));

        $add_header_auth->addSubItem($add_header_name);

        $add_header_value = new ilTextInputGUI($this->getPluginObject()->txt('additional_header_value'), 'add_header_value');
        $add_header_value->setValue($settings->read(Name::ADD_HEADER_VALUE));

        $add_header_auth->addSubItem($add_header_value);
        $form->addItem($add_header_auth);

        $section_soap = new ilFormSectionHeaderGUI();
        $section_soap->setTitle($this->getPluginObject()->txt('credential_section_soap'));
        $form->addItem($section_soap);

        $soap_user = new ilTextInputGUI(
            $this->getPluginObject()->txt('credentials_soap_user'),
            'soap_user'
        );
        $soap_user->setInfo(
            $this->getPluginObject()->txt('credentials_soap_user_info')
        );
        $soap_user->setRequired(true);
        $soap_user->setValue($settings->read(Name::SOAP_USER));
        $form->addItem($soap_user);

        $soap_pass = new ilPasswordInputGUI(
            $this->getPluginObject()->txt('credentials_soap_pass'),
            'soap_pass'
        );
        $soap_pass->setRetype(false);
        $soap_pass->setRequired(true);
        $soap_pass->setValue($settings->read(Name::SOAP_PASS));
        $form->addItem($soap_pass);

        return $form;
    }

    protected function saveCredentials() : void
    {
        $form = $this->initCredentialsForm();
        $settings = $this->veda_factory->settings()->handler();

        try {
            if ($form->checkInput()) {
                $settings->write(Name::REST_URL, (string) $form->getInput('resturl'));
                $settings->write(Name::REST_USER, (string) $form->getInput('restuser'));
                $settings->write(Name::REST_PASSWORD, (string) $form->getInput('restpassword'));
                $settings->write(Name::SOAP_USER, (string) $form->getInput('soap_user'));
                $settings->write(Name::SOAP_PASS, (string) $form->getInput('soap_pass'));
                $settings->write(Name::REST_TOKEN, (string) $form->getInput('authentication_id'));
                $settings->write(Name::PLATTFORM_ID, (string) $form->getInput('platform_id'));
                $settings->writeBool(Name::ADD_HEADER_AUTH, (bool) $form->getInput('add_header_auth'));
                $settings->write(Name::ADD_HEADER_NAME, (string) $form->getInput('add_header_name'));
                $settings->write(Name::ADD_HEADER_VALUE, (string) $form->getInput('add_header_value'));
                $this->tpl->setOnScreenMessage(
                    ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
                    $this->lng->txt('settings_saved'),
                    true
                );
                $this->ctrl->redirect($this, 'credentials');
            }
            $error = $this->lng->txt('err_check_input');
        } catch (ilException $e) {
            $error = $e->getMessage();
            $this->veda_factory->logger()->handler()->error('Error saving credentials: ' . $error);
        }
        $form->setValuesByPost();
        $this->tpl->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $error);
        $this->credentials($form);
    }

    /**
     * @throws ilCtrlException
     */
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

    /**
     * @throws ilCtrlException
     */
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
        $imp_type = new ilRadioGroupInputGUI($this->getPluginObject()->txt('import_selection'), 'selection_' . ImporterInterface::IMPORT_TYPE_SIFA);
        $imp_type->setValue('' . ImporterInterface::IMPORT_NONE);
        $form->addItem($imp_type);

        $none = new ilRadioOption($this->getPluginObject()->txt('import_selection_none'), '' . ImporterInterface::IMPORT_NONE);
        $imp_type->addOption($none);

        $all = new ilRadioOption($this->getPluginObject()->txt('import_selection_all'), '' . ImporterInterface::IMPORT_ALL);
        $imp_type->addOption($all);

        $sel = new ilRadioOption($this->getPluginObject()->txt('import_selection_selected'), '' . ImporterInterface::IMPORT_SELECTED);
        $imp_type->addOption($sel);

        $usr_all = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('import_type_all'),
            ImporterInterface::IMPORT_USR_ALL . '_' . ImporterInterface::IMPORT_TYPE_SIFA
        );
        $usr_all->setValue(ImporterInterface::IMPORT_USR_ALL);
        $sel->addSubItem($usr_all);

        $usr_incremental = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('import_type_incremental'),
            ImporterInterface::IMPORT_USR_INCREMENTAL . '_' . ImporterInterface::IMPORT_TYPE_SIFA
        );
        $usr_incremental->setValue(ImporterInterface::IMPORT_USR_INCREMENTAL);
        $sel->addSubItem($usr_incremental);

        $crs = new ilCheckboxInputGUI($this->lng->txt('objs_crs'), 'crs_' . ImporterInterface::IMPORT_TYPE_SIFA);
        $crs->setValue(ImporterInterface::IMPORT_CRS);
        $sel->addSubItem($crs);

        $mem = new ilCheckboxInputGUI($this->getPluginObject()->txt('type_membership'), 'mem_' . ImporterInterface::IMPORT_TYPE_SIFA);
        $mem->setValue(ImporterInterface::IMPORT_MEM);
        $sel->addSubItem($mem);

        $standard = new ilFormSectionHeaderGUI();
        $standard->setTitle($this->getPluginObject()->txt('section_import_standard'));
        $form->addItem($standard);

        // selection all or single elements
        $imp_type = new ilRadioGroupInputGUI($this->getPluginObject()->txt('import_selection'), 'selection_' . ImporterInterface::IMPORT_TYPE_STANDARD);
        $imp_type->setValue('' . ImporterInterface::IMPORT_NONE);
        $form->addItem($imp_type);

        $none = new ilRadioOption($this->getPluginObject()->txt('import_selection_none'), '' . ImporterInterface::IMPORT_NONE);
        $imp_type->addOption($none);

        $all = new ilRadioOption($this->getPluginObject()->txt('import_selection_all'), '' . ImporterInterface::IMPORT_ALL);
        $imp_type->addOption($all);

        $sel = new ilRadioOption($this->getPluginObject()->txt('import_selection_selected'), '' . ImporterInterface::IMPORT_SELECTED);
        $imp_type->addOption($sel);

        $usr_all = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('import_type_all'),
            ImporterInterface::IMPORT_USR_ALL . '_' . ImporterInterface::IMPORT_TYPE_STANDARD
        );
        $usr_all->setValue(ImporterInterface::IMPORT_USR_ALL);
        $sel->addSubItem($usr_all);

        $usr_incremental = new ilCheckboxInputGUI(
            $this->getPluginObject()->txt('import_type_incremental'),
            ImporterInterface::IMPORT_USR_INCREMENTAL . '_' . ImporterInterface::IMPORT_TYPE_STANDARD,
        );
        $usr_incremental->setValue(ImporterInterface::IMPORT_USR_INCREMENTAL);
        $sel->addSubItem($usr_incremental);

        $crs = new ilCheckboxInputGUI($this->lng->txt('objs_crs'), 'crs_' . ImporterInterface::IMPORT_TYPE_STANDARD);
        $crs->setValue(ImporterInterface::IMPORT_CRS);
        $sel->addSubItem($crs);

        $mem = new ilCheckboxInputGUI($this->getPluginObject()->txt('type_membership'), 'mem_' . ImporterInterface::IMPORT_TYPE_STANDARD);
        $mem->setValue(ImporterInterface::IMPORT_MEM);
        $sel->addSubItem($mem);

        $form->setShowTopButtons(false);

        return $form;
    }

    protected function doImport() : void
    {
        $form = $this->initImportForm();
        if (!$form->checkInput()) {
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $this->lng->txt('err_check_input')
            );
            $this->import($form);
        }
        try {
            foreach ([ImporterInterface::IMPORT_TYPE_SIFA, ImporterInterface::IMPORT_TYPE_STANDARD] as $import_type) {
                $import_type_selection = (int) $form->getInput('selection_' . ((string) $import_type));
                if ($import_type_selection == ImporterInterface::IMPORT_NONE) {
                    continue;
                }
                $modes = [];
                foreach (
                    [
                        ImporterInterface::IMPORT_USR_ALL,
                        ImporterInterface::IMPORT_USR_INCREMENTAL,
                        ImporterInterface::IMPORT_CRS,
                        ImporterInterface::IMPORT_MEM
                    ] as $mode
                ) {
                    if ($form->getInput($mode . '_' . ((string) $import_type))) {
                        $modes[] = $mode;
                    }
                }
                $this->veda_factory->logger()->handler()->dump($modes);
                $this->veda_factory->logger()->handler()->dump($import_type, ilLogLevel::NOTICE);
                $this->veda_factory->logger()->handler()->dump($import_type_selection, ilLogLevel::NOTICE);
                $this->veda_factory->logger()->handler()->dump($modes, ilLogLevel::NOTICE);
                $this->veda_factory->importer()->import(
                    $import_type,
                    ($import_type_selection === ImporterInterface::IMPORT_ALL),
                    $modes
                );
            }
        } catch (Exception $e) {
            $this->veda_factory->logger()->handler()->logStack(ilLogLevel::WARNING);
            $this->veda_factory->logger()->handler()->warning('Import failed with message: ' . $e->getMessage());
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                'Import failed with message: ' . $e->getMessage()
            );
            $this->import($form);
            return;
        }
        $this->tpl->setOnScreenMessage(
            ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
            $this->getPluginObject()->txt('success_import')
        );
        $form->setValuesByPost();
        $this->import($form);
    }

    /**
     * @throws ilException
     */
    protected function importResultUser() : void
    {
        $this->setSubTabs();
        $this->il_tabs->activateTab(self::TAB_IMPORT);
        $this->il_tabs->activateSubTab(self::SUBTAB_IMPORT_USR);
        $table = $this->veda_factory->userStatus()->table()->importResult($this, __FUNCTION__);
        $table->init();
        $table->parse();
        $this->tpl->setContent($table->getHTML());
    }

    /**
     * @throws ilException
     */
    protected function importResultCourse()
    {
        $this->setSubTabs();
        $this->il_tabs->activateTab(self::TAB_IMPORT);
        $this->il_tabs->activateSubTab(self::SUBTAB_IMPORT_CRS);
        $table = $this->veda_factory->coursStatus()->table()->importResult($this, __FUNCTION__);
        $table->init();
        $table->parse();
        $this->tpl->setContent($table->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
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
        if ($this->veda_factory->api()->handler()->testConnection()) {
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
                $this->getPluginObject()->txt('success_api_connect')
            );
        } else {
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                'API Connection Failed'
            );
        }
        $this->credentials();
    }

    protected function testConnectionSoap(): void
    {
        $settings = $this->veda_factory->settings()->handler();
        $client = new ilSoapClient();
        $client->enableWSDL(true);
        if ($client->init()) {
            $session_token = $client->call(
                'login',
                [
                    CLIENT_ID,
                    $settings->read(Name::SOAP_USER),
                    $settings->read(Name::SOAP_PASS)
                ]
            );
            if (stristr($session_token, '::') === false) {
                $this->tpl->setOnScreenMessage(
                    ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                    $this->getPluginObject()->txt('connection_failure_soap')
                );
                $this->credentials();
                return;
            }
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
                $this->getPluginObject()->txt('connection_success_soap') . ' session_token: ' . $session_token
            );
        } else {
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $this->getPluginObject()->txt('connection_failure_soap')
            );
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

    /**
     * @throws ilCtrlException
     */
    protected function migrateUser()
    {
        $oid = $this->http->wrapper()->query()->retrieve('oid', $this->refinery->kindlyTo()->string()) ?? '';
        $login = urldecode($this->http->wrapper()->query()->retrieve('login', $this->refinery->kindlyTo()->string()) ?? '');
        $this->veda_factory->logger()->handler()->debug("Migrating user with oid and login:" .
            "\noid: " . $oid .
            "\nlogin: " . $login
        );
        if ($oid === '' || $login === '') {
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $this->lng->txt('err_check_input'),
                true
            );
            $this->ctrl->redirect($this, 'importResultUser');
        }
        $obj_id_from_oid = ilObjUser::_getImportedUserId($oid);
        $obj_id_from_login = ilObjUser::_loginExists($login);
        $import_id_from_login = ilObject::_lookupImportId($obj_id_from_login);

        if (
            $import_id_from_login != '' ||
            is_null($obj_id_from_login) ||
            $obj_id_from_oid > 0
        ) {
            $msg = 'Migration failed: ' . (is_null($obj_id_from_login) ? 'user does not exist' : 'user already imported');
            $this->veda_factory->logger()->handler()->warning($msg);
            $this->tpl->setOnScreenMessage(
                ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
                $this->lng->txt('err_check_input'),
                true
            );
            $this->ctrl->redirect($this, 'importResultUser');
        }
        ilObjUser::_writeImportId($obj_id_from_login, $oid);
        $user_repo = $this->veda_factory->userStatus()->db()->handler();
        $status = $user_repo->lookupByOId($oid);
        $status = (is_null($status) ? $this->veda_factory->builder()->userStatus()->withOID($oid, false) : $status)
            ->withImportStatusFailed(false);
        $user_repo->update($status);
        $this->tpl->setOnScreenMessage(
            ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS,
            ilVedaConnectorPlugin::getInstance()->txt('migrated_account'),
            true
        );
        $this->ctrl->redirect($this, 'importResultUser');
    }
}
