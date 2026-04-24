<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\Tables;

use ilDatePresentation;
use ilDateTime;
use ilLink;
use ilObject;
use ilTable2GUI;
use ilTemplateException;
use ilTree;
use ilUtil;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Status;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\Type;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface as CourseDBInterface;
use Leifos\VedaConnector\I\CourseStatus\Table\ImportResultInterface;

class ImportResult extends ilTable2GUI implements ImportResultInterface
{
    public function __construct(
        object $class,
        string $method,
        protected ilTree $repository_tree,
        protected ilVedaConnectorPlugin $plugin,
        protected CourseDBInterface $course_db,
    ) {
        $this->setId('vedaimp_res_crs');
        parent::__construct($class, $method);
    }

    public function init(): void
    {
        $this->setTitle($this->plugin->txt('tbl_import_result_crs'));
        $this->setFormAction($this->getFormAction());

        $this->setRowTemplate(
            'tpl.crs_result_row.html',
            $this->plugin->getDirectory()
        );

        $this->addColumn(
            $this->lng->txt('title'),
            'title'
        );
        $this->addColumn(
            $this->lng->txt('type'),
            'type'
        );
        $this->addColumn(
            $this->lng->txt('create_date'),
            'create_date'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_crs_result_created'),
            'created'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_crs_result_pswitch'),
            'pswitch'
        );
        $this->addColumn(
            $this->plugin->txt('tbl_crs_result_tswitch'),
            'tswitch'
        );
    }

    public function parse(): void
    {
        $courses = $this->course_db->lookupAll();
        $rows = [];
        foreach ($courses as $course) {
            $rows[] = [
                'obj_id' => $course->getObjId(),
                'title' => ilObject::_lookupTitle($course->getObjId()),
                'type' => $course->getType(),
                'oid' => $course->getOid(),
                'create_date' => $course->getModified(),
                'created' => $course->getCreationStatus(),
                'pswitch' => $course->getPermanentSwitchRole(),
                'tswitch' => $course->getTemporarySwitchRole()
            ];
        }
        $this->setData($rows);
    }

    /**
     * @throws ilTemplateException
     */
    protected function fillRow(array $a_set): void
    {
        $obj_id = $a_set['obj_id'];
        $refs = ilObject::_getAllReferences($obj_id);
        $ref = end($refs);
        if (!$ref || $this->repository_tree->isDeleted($ref)) {
            $this->tpl->setCurrentBlock('is_deleted');
            $this->tpl->setVariable('TXT_DELETED', $this->lng->txt('deleted'));
        } else {
            $link = ilLink::_getLink($ref);
            $this->tpl->setCurrentBlock('with_title');
            $this->tpl->setVariable('TITLE_LINK', $link);
            $this->tpl->setVariable('TXT_TITLE', ilObject::_lookupTitle($obj_id));
        }
        $this->tpl->parseCurrentBlock();
        $this->tpl->setVariable('TXT_CREATED', ilDatePresentation::formatDate(new ilDateTime($a_set['create_date'], IL_CAL_UNIX)));
        $this->tpl->setVariable(
            'TXT_TYPE',
            $a_set['type'] == Type::SIFA ?
                $this->plugin->txt('type_sifa') :
                $this->plugin->txt('type_standard')
        );
        $this->tpl->setVariable(
            'CREATED_IMG',
            $a_set['created'] == Status::SYNCHRONIZED ?
                ilUtil::getImagePath('standard/icon_ok.svg') :
                ilUtil::getImagePath('standard/icon_not_ok.svg')
        );

        if (ilObject::_exists($a_set['tswitch'])) {
            $this->tpl->setVariable('TXT_TAVAILABLE', $this->plugin->txt('role_available'));
        } else {
            $this->tpl->setVariable('TXT_TAVAILABLE', $this->plugin->txt('role_unavailable'));
        }
        if (ilObject::_exists($a_set['pswitch'])) {
            $this->tpl->setVariable('TXT_PAVAILABLE', $this->plugin->txt('role_available'));
        } else {
            $this->tpl->setVariable('TXT_PAVAILABLE', $this->plugin->txt('role_unavailable'));
        }
        $this->tpl->setVariable('OID', $a_set['oid']);
    }
}
