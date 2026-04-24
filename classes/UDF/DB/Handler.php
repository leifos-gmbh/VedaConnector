<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UDF\DB;

use ilDBConstants;
use ilDBInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;
use Leifos\VedaConnector\I\UDF\DB\HandlerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected SettingsInterface $settings
    ) {
    }

    /**
     * @return int[]
     */
    public function getUsersForTutorId(?string $tutor_oid): array
    {
        return $this->getUserIdsForFieldAndOId($tutor_oid, $this->settings->readAsInt(SettingsName::UDF_TUTOR_ID));
    }

    /**
     * @return int[]
     */
    public function getUsersForCompanionId(?string $companion_oid): array
    {
        return $this->getUserIdsForFieldAndOId($companion_oid, $this->settings->readAsInt(SettingsName::UDF_COMPANION_ID));
    }

    /**
     * @return int[]
     */
    public function getUsersForSupervisorId(?string $supervisor_oid): array
    {
        return $this->getUserIdsForFieldAndOId($supervisor_oid, $this->settings->readAsInt(SettingsName::UDF_SUPERVISOR_ID));
    }

    public function getUserIdsForFieldAndOId(
        ?string $oid,
        int $field_id
    ): array {
        $query = sprintf('select usr_id from udf_text where field_id = %s and value = %s',
            $this->db->quote($field_id, ilDBConstants::T_INTEGER),
            $this->db->quote($oid, ilDBConstants::T_TEXT)
        );
        #$query = 'select usr_id from udf_text ' .
        #    'where field_id = ' . $this->db->quote($this->fields[$field->value], ilDBConstants::T_INTEGER) . ' ' .
        #    'and value = ' . $this->db->quote($oid, ilDBConstants::T_TEXT);
        $res = $this->db->query($query);
        $user_ids = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $user_ids[] = (int) $row->usr_id;
        }
        return $user_ids;
    }
}
