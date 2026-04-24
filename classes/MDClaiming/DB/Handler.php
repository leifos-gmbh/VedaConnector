<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\MDClaiming\DB;

use ilDBConstants;
use ilDBInterface;
use ilLogLevel;
use ilObjCourse;
use ilObject;
use ilObjectFactory;
use ilTree;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\MDClaiming\DB\HandlerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;

class Handler implements HandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected SettingsInterface $settings,
        protected ilDBInterface $db,
        protected ilTree $repository_tree
    ) {
    }

    public function lookupSegmentId(
        int $ref_id
    ) : string {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('SELECT value FROM adv_md_values_ltext WHERE field_id = %s AND obj_id = %s AND value != %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANGABSCHNITT), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER),
            $this->db->quote('', ilDBConstants::T_TEXT)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select value from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSGANGABSCHNITT->value], ilDBConstants::T_INTEGER) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER) . ' ' .
        #    'and value != ' . $this->db->quote('', ilDBConstants::T_TEXT);
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->value;
        }
        return '';
    }

    public function lookupTrainingCourseId(
        int $ref_id
    ) : string {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('SELECT value FROM adv_md_values_ltext WHERE field_id = %s AND obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANG), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select value from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSGANG->value], ilDBConstants::T_INTEGER) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if ($row->value) {
                return $row->value;
            }
        }
        return '';
    }

    public function deleteTrainingCourseSegmentId(
        int $ref_id
    ) : void {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('delete from adv_md_values_ltext where field_id = %s and obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANGABSCHNITT), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'delete from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSGANGABSCHNITT->value], ilDBConstants::T_INTEGER) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $this->db->manipulate($query);
    }

    public function deleteTrainingCourseSegmentTrainId(
        int $ref_id
    ) : void {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('DELETE FROM adv_md_values_ltext WHERE field_id = %s AND obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSZUGABSCHNITT), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'delete from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSZUGABSCHNITT->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $this->db->manipulate($query);
    }

    public function writeTrainingCourseSegmentTrainId(
        int $target_id,
        string $tc_oid
    ) : void {
        $obj_id = ilObject::_lookupObjId($target_id);
        $query = sprintf('INSERT INTO adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) VALUES (%s, %s, %s, %s, %s)',
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER),
            $this->db->quote($this->settings->read(SettingsName::MD_FIELD_AUSBILDUNGSZUGABSCHNITT), ilDBConstants::T_TEXT),
            $this->db->quote('', ilDBConstants::T_TEXT),
            $this->db->quote($tc_oid, ilDBConstants::T_TEXT),
            $this->db->quote(1, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) ' .
        #    'values ( ' .
        #    $this->db->quote($obj_id, ilDBConstants::T_INTEGER) . ', ' .
        #    $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSZUGABSCHNITT->value], ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote('', ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote($tc_oid, ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote(1, ilDBConstants::T_INTEGER) .
        #    ')';
        $this->db->manipulate($query);
        $query = sprintf('insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) values (%s, %s, %s, %s, %s)',
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER),
            $this->db->quote($this->settings->read(SettingsName::MD_FIELD_AUSBILDUNGSZUGABSCHNITT), ilDBConstants::T_TEXT),
            $this->db->quote('de', ilDBConstants::T_TEXT),
            $this->db->quote($tc_oid, ilDBConstants::T_TEXT),
            $this->db->quote(1, ilDBConstants::T_INTEGER)
        );
        #$query = 'insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) ' .
        #    'values ( ' .
        #    $this->db->quote($obj_id, ilDBConstants::T_INTEGER) . ', ' .
        #    $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSZUGABSCHNITT->value], ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote('de', ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote($tc_oid, ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote(1, ilDBConstants::T_INTEGER) .
        #    ')';
        $this->db->manipulate($query);
    }

    public function deleteTrainingCourseId(
        int $ref_id
    ) : void {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('DELETE FROM adv_md_values_ltext WHERE field_id = %s AND obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANG), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'delete from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSGANG->value], ilDBConstants::T_INTEGER) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $this->db->manipulate($query);
    }

    public function deleteTrainingCourseTrainId(
        int $ref_id
    ) : void {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('DELETE FROM adv_md_values_ltext WHERE field_id = %s AND obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSZUG), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'delete from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSZUG->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $this->db->manipulate($query);
    }

    public function writeTrainingCourseTrainId(
        int $target_id,
        string $tc_oid
    ) : void {
        $obj_id = ilObject::_lookupObjId($target_id);
        $query = sprintf('insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) values (%s, %s, %s, %s, %s)',
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER),
            $this->db->quote($this->settings->read(SettingsName::MD_FIELD_AUSBILDUNGSZUG), ilDBConstants::T_TEXT),
            $this->db->quote('', ilDBConstants::T_TEXT),
            $this->db->quote($tc_oid, ilDBConstants::T_TEXT),
            $this->db->quote(1, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) ' .
        #    'values ( ' .
        #    $this->db->quote($obj_id, ilDBConstants::T_INTEGER) . ', ' .
        #    $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSZUG->value], ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote('', ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote($tc_oid, ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote(1, ilDBConstants::T_INTEGER) .
        #    ')';
        $this->logger->debug($query);
        $this->db->manipulate($query);
        $query = sprintf('insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) values (%s, %s, %s, %s, %s)',
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER),
            $this->db->quote($this->settings->read(SettingsName::MD_FIELD_AUSBILDUNGSZUG), ilDBConstants::T_TEXT),
            $this->db->quote('de', ilDBConstants::T_TEXT),
            $this->db->quote($tc_oid, ilDBConstants::T_TEXT),
            $this->db->quote(1, ilDBConstants::T_INTEGER)
        );
        #$query = 'insert into adv_md_values_ltext (obj_id, field_id, value_index, value, disabled) ' .
        #    'values ( ' .
        #    $this->db->quote($obj_id, ilDBConstants::T_INTEGER) . ', ' .
        #    $this->db->quote($fields[MDClaimingFields::AUSBILDUNGSZUG->value], ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote('de', ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote($tc_oid, ilDBConstants::T_TEXT) . ', ' .
        #    $this->db->quote(1, ilDBConstants::T_INTEGER) .
        #    ')';
        $this->logger->debug($query);
        $this->db->manipulate($query);
    }

    public function findTrainingCourseId(
        int $ref_id
    ) : string {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('select value from adv_md_values_ltext where field_id = %s and obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANG), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select value from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSGANG->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and obj_id = ' . $this->db->quote($obj_id, ilDBConstants::T_INTEGER);
        $res = $this->db->query($query);
        $this->logger->dump($query, ilLogLevel::INFO);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if ($row->value) {
                return $row->value;
            }
        }
        return '';
    }

    /**
     * @return string[]
     */
    public function findTrainingCourseTrains() : array
    {
        $query = sprintf('select value from adv_md_values_ltext where field_id = %s and value != %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSZUG), ilDBConstants::T_INTEGER),
            $this->db->quote('', ilDBConstants::T_TEXT)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select value from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSZUG->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and value != ' . $this->db->quote('', ilDBConstants::T_TEXT);
        $res = $this->db->query($query);
        $oids = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if (!in_array($row->value, $oids)) {
                $oids[] = $row->value;
            }
        }
        return $oids;
    }

    public function findTrainingCourseTrain(
        ?string $oid
    ) : int {
        $query = sprintf('select obj_id from adv_md_values_ltext where field_id = %s and value = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSZUG), ilDBConstants::T_INTEGER),
            $this->db->quote($oid, ilDBConstants::T_TEXT)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select obj_id from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSZUG->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and value = ' . $this->db->quote($oid, ilDBConstants::T_TEXT);
        $this->logger->alert($query);
        $res = $this->db->query($query);

        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            // find ref_id
            $refs = ilObject::_getAllReferences($row->obj_id);
            $ref = end($refs);
            $object = ilObjectFactory::getInstanceByRefId($ref, false);
            if (!$object instanceof ilObjCourse) {
                $this->logger->error('Found invalid "Ausbildungszug" with obj_id: ' . $row->obj_id);
                continue;
            }
            return $object->getRefId();
        }
        return 0;
    }

    /**
     * @return int[]
     */
    public function findTrainingCourseTemplates() : array
    {
        $query = sprintf('select obj_id from adv_md_values_ltext where field_id = %s and value != %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSGANG), ilDBConstants::T_INTEGER),
            $this->db->quote('', ilDBConstants::T_TEXT)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select obj_id from adv_md_values_ltext ' . ' ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSGANG->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and value != ' . $this->db->quote('', ilDBConstants::T_TEXT);
        $res = $this->db->query($query);
        $template_references = [];
        $obj_ids = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if (in_array($row->obj_id, $obj_ids)) {
                continue;
            }
            $obj_ids[] = $row->obj_id;

            // find ref_id
            $refs = ilObject::_getAllReferences($row->obj_id);
            $ref = end($refs);

            $object = ilObjectFactory::getInstanceByRefId($ref, false);
            if (!$object instanceof ilObjCourse) {
                $this->logger->error('Found invalid "Ausbildungsgang" with obj_id: ' . $row->obj_id);
                continue;
            }
            if ($this->repository_tree->isDeleted($object->getRefId())) {
                $this->logger->notice('Ignoring deleted course with obj_id: ' . $row->obj_id);
                continue;
            }
            $template_references[] = $object->getRefId();
        }

        return $template_references;
    }

    public function findTrainSegmentId(
        int $ref_id
    ) : string {
        $obj_id = ilObject::_lookupObjId($ref_id);
        $query = sprintf('select value from adv_md_values_ltext where field_id = %s and obj_id = %s',
            $this->db->quote($this->settings->readAsInt(SettingsName::MD_FIELD_AUSBILDUNGSZUGABSCHNITT), ilDBConstants::T_INTEGER),
            $this->db->quote($obj_id, ilDBConstants::T_INTEGER)
        );
        #$fields = $this->claiming_plugin->getFields();
        #$query = 'select value from adv_md_values_ltext ' .
        #    'where field_id = ' . $this->db->quote(
        #        $fields[MDClaimingFields::AUSBILDUNGSZUGABSCHNITT->value],
        #        ilDBConstants::T_INTEGER
        #    ) . ' ' .
        #    'and obj_id = ' . $this->db->quote(
        #        $obj_id,
        #        ilDBConstants::T_INTEGER
        #    );
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if ($row->value) {
                return $row->value;
            }
        }
        return '';
    }
}
