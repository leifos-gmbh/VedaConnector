<?php

declare(strict_types=1);

namespace Leifos\VedaConnector;

use ilTemplate;
use ilTemplateException;
use ilTree;
use Leifos\VedaConnector\I\Api\HandlerInterface;
use ilVedaConnectorPlugin;
use ilVedaMDClaimingPluginDBManager;
use Leifos\VedaConnector\I\IdValidatorInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerHandlerInterface;

class IdValidator implements IdValidatorInterface
{
    protected ilTemplate $err_template;

    public function __construct(
        protected int $ref_id,
        protected ilTree $il_tree,
        protected ilVedaConnectorPlugin $plugin,
        protected ilVedaMDClaimingPluginDBManager $claiming_db_manager,
        protected LoggerHandlerInterface $logger,
        protected HandlerInterface $my_api
    ) {
        $this->err_template = $this->plugin->getTemplate('tpl.validation_error.html');
    }

    /**
     * @throws ilTemplateException
     */
    public function validate() : bool
    {
        $course_oid = $this->claiming_db_manager->lookupTrainingCourseId($this->ref_id);
        if (!$this->validateTrainingCourseId($course_oid)) {
            return false;
        }
        $sessions = $this->readLocalSessions();
        $exercises = $this->readLocalExercises();
        if (
            !$this->validateLocalSessions($sessions, $course_oid) ||
            !$this->validateRemoteSessions($sessions, $course_oid) ||
            !$this->validateLocalExercises($exercises, $course_oid) ||
            !$this->validateRemoteExercises($exercises, $course_oid)
        ) {
            return false;
        }
        return true;
    }

    protected function readLocalSessions() : array
    {
        $subtree = $this->il_tree->getSubTree(
            $this->il_tree->getNodeData($this->ref_id),
            true,
            ['sess']
        );
        $sessions = [];
        foreach ($subtree as $index => $node) {
            $sessions[$index] = $node;
            $sessions[$index]['vedaid'] = $this->claiming_db_manager->lookupSegmentId($node['ref_id']);
        }
        return $sessions;
    }

    protected function readLocalExercises() : array
    {
        $subtree = $this->il_tree->getSubTree(
            $this->il_tree->getNodeData($this->ref_id),
            true,
            ['exc']
        );
        $exercises = [];
        foreach ($subtree as $index => $node) {
            $exercises[$index] = $node;
            $exercises[$index]['vedaid'] = $this->claiming_db_manager->lookupSegmentId($node['ref_id']);
        }
        return $exercises;
    }

    /**
     * @throws ilTemplateException
     */
    protected function validateLocalSessions(array $sessions, string $course_id) : bool
    {
        $missing = $this->my_api->validateLocalSessions($sessions, $course_id);
        if (count($missing)) {
            foreach ($missing as $index => $node) {
                $this->err_template->setCurrentBlock('sess_remote_item');
                $this->err_template->setVariable('SESS_REMOTE_ITEM_TITLE', $node['title']);
                $this->err_template->setVariable('SESS_REMOTE_ITEM_OID', $node['vedaid']);
                $this->err_template->parseCurrentBlock();
            }
            $this->err_template->setCurrentBlock('sess_remote');
            $this->err_template->setVariable('SESS_INFO_REMOTE', $this->plugin->txt('err_val_sess_remote_info'));
            $this->err_template->parseCurrentBlock();
            return false;
        }
        return true;
    }

    /**
     * @throws ilTemplateException
     */
    protected function validateLocalExercises(array $exercises, string $course_id) : bool
    {
        $missing = $this->my_api->validateLocalExercises($exercises, $course_id);
        if (count($missing)) {
            foreach ($missing as $index => $node) {
                $this->err_template->setCurrentBlock('exc_remote_item');
                $this->err_template->setVariable('EXC_REMOTE_ITEM_TITLE', $node['title']);
                $this->err_template->setVariable('EXC_REMOTE_ITEM_OID', $node['vedaid']);
                $this->err_template->parseCurrentBlock();
            }
            $this->err_template->setCurrentBlock('exc_remote');
            $this->err_template->setVariable('EXC_INFO_REMOTE', $this->plugin->txt('err_val_exc_remote_info'));
            $this->err_template->parseCurrentBlock();
            return false;
        }
        return true;
    }

    /**
     * @throws ilTemplateException
     */
    protected function validateRemoteSessions(array $sessions, string $course_oid) : bool
    {
        $missing = $this->my_api->validateRemoteSessions($sessions, $course_oid);
        if (count($missing)) {
            foreach ($missing as $oid => $title) {
                $this->err_template->setCurrentBlock('sess_local_item');
                $this->err_template->setVariable('SESS_LOCAL_ITEM_TITLE', $title);
                $this->err_template->setVariable('SESS_LOCAL_ITEM_OID', $oid);
                $this->err_template->parseCurrentBlock();
            }
            $this->err_template->setCurrentBlock('sess_local');
            $this->err_template->setVariable('SESS_INFO_LOCAL', $this->plugin->txt('err_val_sess_local_info'));
            $this->err_template->parseCurrentBlock();
            return false;
        }
        return true;
    }

    /**
     * @throws ilTemplateException
     */
    protected function validateRemoteExercises(array $exercises, string $course_oid) : bool
    {
        $missing = $this->my_api->validateRemoteExercises($exercises, $course_oid);
        if (count($missing)) {
            foreach ($missing as $oid => $title) {
                $this->err_template->setCurrentBlock('exc_local_item');
                $this->err_template->setVariable('EXC_LOCAL_ITEM_TITLE', $title);
                $this->err_template->setVariable('EXC_LOCAL_ITEM_OID', $oid);
                $this->err_template->parseCurrentBlock();
            }
            $this->err_template->setCurrentBlock('exc_local');
            $this->err_template->setVariable('EXC_INFO_LOCAL', $this->plugin->txt('err_val_exc_local_info'));
            $this->err_template->parseCurrentBlock();
            return false;
        }
        return true;
    }

    /**
     * @throws ilTemplateException
     */
    public function getErrorMessage() : string
    {
        return $this->err_template->get();
    }

    public function getSuccessMessage() : string
    {
        return $this->plugin->txt('success_validation');
    }

    protected function validateTrainingCourseId(string $course_id) : bool
    {
        if (!$course_id) {
            $this->err_template->setVariable('SIMPLE_FAILURE', $this->plugin->txt('err_val_no_tc_id'));
            return false;
        }
        if ($this->my_api->isTrainingCourseValid($course_id)) {
            return true;
        }
        $this->err_template->setVariable('SIMPLE_FAILURE', $this->plugin->txt('err_val_wrong_tc'));
        return false;
    }
}
