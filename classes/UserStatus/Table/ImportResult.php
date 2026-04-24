<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\Table;

use ilAdvancedSelectionListGUI;
use ilTable2GUI;
use ilUtil;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface as UserDBInterface;
use Leifos\VedaConnector\I\UserStatus\Table\ImportResultInterface;

class ImportResult extends ilTable2GUI implements ImportResultInterface
{
    public function __construct(
        object $class,
        string $method,
        protected ilVedaConnectorPlugin $plugin,
        protected UserDBInterface $user_db,
    ) {
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
            'login', '40%'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_created'),
            'created', "7%"
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_pwd_changed'),
            'pwd', "7%"
        );
        $this->addColumn(
            $this->plugin->txt('tbl_usr_result_import_failure'),
            'failure', "36%"
        );
        $this->addColumn($this->lng->txt('actions'), "", '10%');
    }

    public function parse() : void
    {
        $users = $this->user_db->lookupAll();
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

    protected function fillRow(array $a_set): void
    {
        $this->tpl->setVariable('TXT_LOGIN', $a_set['login']);
        $this->tpl->setVariable('OID', $a_set['oid']);
        $this->tpl->setVariable(
            'CREATED_IMG',
            $a_set['created'] == Status::SYNCHRONIZED ?
                ilUtil::getImagePath('standard/icon_ok.svg') :
                ilUtil::getImagePath('standard/icon_not_ok.svg')
        );
        $this->tpl->setVariable(
            'PWD_IMG',
            $a_set['pwd'] == Status::SYNCHRONIZED ?
                ilUtil::getImagePath('standard/icon_ok.svg') :
                ilUtil::getImagePath('standard/icon_not_ok.svg')
        );

        if ($a_set['failure']) {
            $this->tpl->setVariable('FAILURE_TXT', $this->plugin->txt('err_import_usr_duplicate'));
            $list = new ilAdvancedSelectionListGUI();
            $list->setId('veda_oid_' . $a_set['login'] . '_' . $a_set['oid']);
            $list->setListTitle($this->lng->txt('actions'));

            $this->ctrl->setParameterByClass(
                'ilVedaConnectorConfigGUI',
                'oid',
                $a_set['oid'] ?? ''
            );
            $this->ctrl->setParameterByClass(
                'ilVedaConnectorConfigGUI',
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
            $this->ctrl->clearParameterByClass('ilVedaConnectorConfigGUI', 'oid');
            $this->ctrl->clearParameterByClass('ilVedaConnectorConfigGUI', 'login');
            $this->tpl->setVariable('SELECTION', $list->getHTML());
        }
    }
}
