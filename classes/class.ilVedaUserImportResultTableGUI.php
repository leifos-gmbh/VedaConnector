<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Table GUI for user import results
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 *
 */
class ilVedaUserImportResultTableGUI extends \ilTable2GUI
{
    private ?ilVedaConnectorPlugin $plugin = null;

    /**
     * ilVedaUserImportResultTableGUI constructor.
     * @param object $class
     * @param string $method
     */
    public function __construct($class, string $method)
    {
        $this->plugin = ilVedaConnectorPlugin::getInstance();
        $this->setId('vedaimp_res_usr');
        parent::__construct($class, $method);
    }

    public function init() : void
    {
        $this->setTitle($this->plugin->txt('tbl_import_result_usr'));
        $this->setFormAction($this->getFormAction());

        $this->setRowTemplate(
            'tpl.usr_result_row.html',
            $this->plugin->getDirectory()
        );

        $this->addColumn(
            $this->lng->txt('username'),
            'login'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_created'),
            'created'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_pwd_changed'),
            'pwd'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_import_failure'),
            'failure'
        );
        $this->addColumn(
            $this->lng->txt('actions'),
            ''
        );
    }

    /**
     * Parse imported user data
     * @throws \ilDatabaseException
     */
    public function parse() : void
    {
        $users = (new ilVedaRepositoryFactory())->getUserRepository()->lookupAllUsers();
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                'login' => $user->getLogin(),
                'oid' => $user->getOid(),
                'created' => $user->getCreationStatus(),
                'pwd' => $user->getPasswordStatus(),
                'failure' => $user->isImportFailure()
            ];
        }
        $this->setData($rows);
    }

    protected function fillRow($a_set)
    {
        $this->tpl->setVariable('TXT_LOGIN', $a_set['login']);
        $this->tpl->setVariable('OID', $a_set['oid']);
        $this->tpl->setVariable(
            'CREATED_IMG',
            $a_set['created'] == ilVedaUserStatus::SYNCHRONIZED ?
                ilUtil::getImagePath('icon_ok.svg') :
                ilUtil::getImagePath('icon_not_ok.svg')
        );
        $this->tpl->setVariable(
            'PWD_IMG',
            $a_set['pwd'] == ilVedaUserStatus::SYNCHRONIZED ?
                ilUtil::getImagePath('icon_ok.svg') :
                ilUtil::getImagePath('icon_not_ok.svg')
        );

        if ($a_set['failure']) {
            $this->tpl->setVariable('FAILURE_TXT', $this->plugin->txt('err_import_usr_duplicate'));
            $list = new ilAdvancedSelectionListGUI();
            $list->setId('veda_oid_' . $a_set['login'] . '_' . $a_set['oid']);
            $list->setListTitle($this->lng->txt('actions'));

            $this->ctrl->setParameter(
                $this->getParentObject(),
                'oid',
                $a_set['oid']
            );
            $this->ctrl->setParameter(
                $this->getParentObject(),
                'login',
                urlencode($a_set['login'])
            );
            $list->addItem(
                ilVedaConnectorPlugin::getInstance()->txt('migrate_user'),
                '',
                $this->ctrl->getLinkTarget(
                    $this->getParentObject(),
                    'migrateUser'
                )
            );
            $this->tpl->setVariable('SELECTION', $list->getHTML());
        }
    }
}
