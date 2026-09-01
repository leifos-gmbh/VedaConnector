<#1>
<?php
if(!$ilDB->tableExists('cron_crnhk_vedaimp_us'))
{
	$ilDB->createTable('cron_crnhk_vedaimp_us',
		array(
			'oid'	=>
				array(
					'type'		=> 'text',
					'length'	=> 64,
					'notnull'	=> false
				),
			'login'	=>
				array(
					'type'		=> 'text',
					'length'	=> 64,
					'notnull'	=> false
				),
			'status_pwd'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 1,
					'notnull'	=> true
				),
			'status_created'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 1,
					'notnull'	=> true
				),
			'import_failure'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 1,
					'notnull'	=> true
				)
		)
	);
	$ilDB->addPrimaryKey('cron_crnhk_vedaimp_us',['oid']);
}
?>
<#2>
<?php
// do nothing
?>
<#3>
<?php
if(!$ilDB->tableExists('cron_crnhk_vedaimp_crs'))
{
	$ilDB->createTable('cron_crnhk_vedaimp_crs',
		array(
			'oid'	=>
				array(
					'type'		=> 'text',
					'length'	=> 64,
					'notnull'	=> false
				),
			'switchp'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 4,
					'notnull'	=> true,
					'default'   => 0
				),
			'switcht'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 4,
					'notnull'	=> true,
					'default'   => 0
				),
			'status_created'	=>
				array(
					'type'		=> 'integer',
					'length'	=> 2,
					'notnull'	=> true
				)
		)
	);
	$ilDB->addPrimaryKey('cron_crnhk_vedaimp_crs',['oid']);
}
?>
<#4>
<?php
if (!$ilDB->tableColumnExists('cron_crnhk_vedaimp_crs', 'obj_id'))
{
	$ilDB->addTableColumn(
			'cron_crnhk_vedaimp_crs',
			'obj_id',
			[
				"type" => "integer",
				"notnull" => true,
				"length" => 4,
				"default" => 0
			]
	);
}
?>

<#5>
<?php
// nothing
?>
?>
<#6>
<?php
if(!$ilDB->tableExists('cron_crnhk_vedaimp_seg')) {
	$ilDB->createTable(
		'cron_crnhk_vedaimp_seg',
		[
			'oid' =>
				[
					'type' => 'text',
					'length' => 64,
					'notnull' => false
				],
			'type' =>
				[
					'type' => 'text',
					'length' => 64,
					'notnull' => false
				]
		]
	);
	$ilDB->addPrimaryKey('cron_crnhk_vedaimp_seg',['oid','type']);
}
?>
<#7>
<?php
if (!$ilDB->tableColumnExists('cron_crnhk_vedaimp_crs', 'modified')) {
    $ilDB->addTableColumn(
        'cron_crnhk_vedaimp_crs',
        'modified',
        [
            "type" => ilDBConstants::T_INTEGER,
            "notnull" => true,
            "length" => 8,
            "default" => 0
        ]
    );
}
?>
<#8>
<?php
if (!$ilDB->tableColumnExists('cron_crnhk_vedaimp_crs', 'type')) {
    $ilDB->addTableColumn(
        'cron_crnhk_vedaimp_crs',
        'type',
        [
            "type" => ilDBConstants::T_INTEGER,
            "notnull" => true,
            "length" => 2,
            "default" => 0
        ]
    );
}
?>
<#9>
<?php
if ($ilDB->tableExists('adv_md_values_text') &&
    $ilDB->tableExists('adv_md_values_ltext')
) {
    // inserts all values from adv_md_values_text into adv_md_values_ltext WITHOUT
    // adv_md_values_ltext.value_index, ignoring duplicate entries.
    $ilDB->manipulate("
        INSERT IGNORE INTO adv_md_values_ltext (field_id, obj_id, `value`, value_index, disabled, sub_type, sub_id)
            SELECT val.field_id, val.obj_id, val.value, '', val.disabled, val.sub_type, val.sub_id
                FROM adv_md_values_text AS val
        ;
    ");

    // inserts all values from adv_md_values_text into adv_md_values_ltext WITH
    // adv_md_values_ltext.value_index, whereas the value_index will be the default
    // lang-code of adv_md_field_int because the old table didn't store this information.
    $ilDB->manipulate("
        INSERT IGNORE INTO adv_md_values_ltext (field_id, obj_id, `value`, value_index, disabled, sub_type, sub_id)
            SELECT val.field_id, val.obj_id, val.value, field.lang_code, val.disabled, val.sub_type, val.sub_id
                FROM adv_md_values_text AS val
                JOIN adv_md_field_int AS field ON field.field_id = val.field_id
        ;
    ");
}
?>
<#10>
<?php
// Creates column document success to save the 'kursabschlussAlsErfolgDokumentieren' state.
if (
        $ilDB->tableExists('cron_crnhk_vedaimp_crs') &&
        !$ilDB->tableColumnExists('cron_crnhk_vedaimp_crs', 'document_success')
) {
    $ilDB->addTableColumn(
        'cron_crnhk_vedaimp_crs',
        'document_success',
        [
            "type" => ilDBConstants::T_INTEGER,
            "notnull" => true,
            "length" => 2,
            "default" => 0
        ]
    );
}
?>
<#11>
<?php
if (
    !$ilDB->tableExists('cron_crnhk_vedaimp_ml')
) {
    $ilDB->createTable(
        'cron_crnhk_vedaimp_ml',
        [
            'id' =>
                [
                    'type' => ilDBConstants::T_INTEGER,
                    'length' => 4,
                    'notnull' => false
                ],
            'msg' =>
                [
                    'type' => ilDBConstants::T_TEXT,
                    'length' => 128,
                    'notnull' => false
                ],
            'type' =>
                [
                    "type" => ilDBConstants::T_INTEGER,
                    "notnull" => true,
                    "length" => 4,
                    "default" => 0
                ],
            'modified' =>
                [
                    "type" => ilDBConstants::T_TIMESTAMP,
                    "notnull" => false
                ]
        ]
    );
    $ilDB->addPrimaryKey('cron_crnhk_vedaimp_ml',['id']);
    $ilDB->createSequence('cron_crnhk_vedaimp_ml');
}
?>
<#12>
<?php
if ($ilDB->tableColumnExists('cron_crnhk_vedaimp_ml', 'msg')) {
    $ilDB->modifyTableColumn(
        'cron_crnhk_vedaimp_ml',
        'msg',
        [
                'type' => ilDBConstants::T_TEXT,
                'length' => 1000
        ]
    );
}
?>
<#13>
<?php
$query = "SELECT value FROM settings WHERE module = 'vedaudfclaiming' AND settings.keyword = 'fields'";
$result = $ilDB->query($query);
$remaining_udf_field_names = [];

# migration udf field values
if (!is_null($row = $result->fetchAssoc())) {
    $map = unserialize($row['value']);
    foreach (['supervisor', 'supervisor_mail', 'member_id', 'tutor_id', 'companion_id', 'supervisor_id'] as $field_name) {
        if (isset($map[$field_name])) {
            $ilDB->insert('settings', [
                    'module' => ['text', 'vedaimp'],
                    'keyword' => ['text', $field_name],
                    'value' => ['text', '' . $map[$field_name]]
            ]);
        } else {
            $remaining_udf_field_names[] = $field_name;
        }
    }
} else {
    $remaining_udf_field_names = ['supervisor', 'supervisor_mail', 'member_id', 'tutor_id', 'companion_id', 'supervisor_id'];
}


$query = "SELECT * FROM settings WHERE  module = 'vedaimp' AND " . $ilDB->in('keyword', $remaining_udf_field_names, false, ilDBConstants::T_TEXT);
$result = $ilDB->query($query);
while ($row = $result->fetchAssoc()) {
    $remaining_udf_field_names = array_diff($remaining_udf_field_names, [$row['keyword']]);
}

# Create udf fields that do not exist
foreach ($remaining_udf_field_names as $field_name) {
    $next_id = null;
    switch ($field_name) {
        case 'supervisor':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Aufsichtsperson'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 1],
                    'export' => ['integer', 1],
                    'course_export' => ['integer', 1],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 1],
                    'changeable_lua' => ['integer', 1],
                    'group_export' => ['integer', 1],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
        case 'supervisor_mail':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Aufsichtsperson e-Mail'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 1],
                    'export' => ['integer', 1],
                    'course_export' => ['integer', 1],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 1],
                    'changeable_lua' => ['integer', 1],
                    'group_export' => ['integer', 1],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
        case 'member_id':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Mitgliedsnummer'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 1],
                    'export' => ['integer', 1],
                    'course_export' => ['integer', 1],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 1],
                    'changeable_lua' => ['integer', 1],
                    'group_export' => ['integer', 1],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
        case 'tutor_id':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Dozenten-ID'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 0],
                    'export' => ['integer', 0],
                    'course_export' => ['integer', 0],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 0],
                    'changeable_lua' => ['integer', 0],
                    'group_export' => ['integer', 0],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
        case 'companion_id':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Lernbegleiter-ID'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 0],
                    'export' => ['integer', 0],
                    'course_export' => ['integer', 0],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 0],
                    'changeable_lua' => ['integer', 0],
                    'group_export' => ['integer', 0],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
        case 'supervisor_id':
            $next_id = $ilDB->nextId('udf_definition');
            $ilDB->insert('udf_definition', [
                    'field_id' => ['integer', $next_id],
                    'field_name' => ['text', 'Aufsichtsperson-ID'],
                    'field_type' => ['integer', 1],
                    'field_values' => ['clob',  'a:0:{}'],
                    'visible' => ['integer', 1],
                    'changeable' => ['integer', 0],
                    'required' => ['integer', 0],
                    'searchable' => ['integer', 0],
                    'export' => ['integer', 0],
                    'course_export' => ['integer', 0],
                    'registration_visible' => ['integer', 0],
                    'visible_lua' => ['integer', 0],
                    'changeable_lua' => ['integer', 0],
                    'group_export' => ['integer', 0],
                    'certificate' => ['integer', 0],
                    'prg_export' => ['integer', 0]
            ]);
            break;
    }
    if (is_null($next_id)) {
        continue;
    }
    $ilDB->insert('settings', [
            'module' => ['text', 'vedaimp'],
            'keyword' => ['text', $field_name],
            'value' => ['text', '' . $next_id]
    ]);
}
?>
<#14>
<?php
$query = "SELECT value FROM settings WHERE module = 'vedaclaiming' AND settings.keyword = 'records'";
$result = $ilDB->query($query);
$remaining_md_records = [];

# migration if md records
if (!is_null($row = $result->fetchAssoc())) {
    $map = unserialize($row['value']);
    foreach (['Sifa-Ausbildung', 'Sifa-Abschnitt'] as $field_name) {
        if (isset($map[$field_name])) {
            $ilDB->insert('settings', [
                    'module' => ['text', 'vedaimp'],
                    'keyword' => ['text', $field_name],
                    'value' => ['text', '' . $map[$field_name]]
            ]);
        } else {
            $remaining_md_records[] = $field_name;
        }
    }
} else {
    $remaining_md_records = ['Sifa-Ausbildung', 'Sifa-Abschnitt'];
}

$query = "SELECT * FROM settings WHERE module = 'vedaimp' AND " . $ilDB->in('keyword', $remaining_md_records, false, ilDBConstants::T_TEXT);
$result = $ilDB->query($query);
while ($row = $result->fetchAssoc()) {
    $remaining_md_records = array_diff($remaining_md_records, [$row['keyword']]);
}

# create md records that do not exist
foreach ($remaining_md_records as $field_name) {
    $next_id = null;
    switch ($field_name) {
        case 'Sifa-Ausbildung':
            $next_id = ilAdvancedMDClaimingPlugin::createDBRecord(
                $field_name,
               '',
                true,
                ['crs']
            );
            break;
        case 'Sifa-Abschnitt':
            $next_id = ilAdvancedMDClaimingPlugin::createDBRecord(
                $field_name,
                '',
                true,
                ['exc','sess']
            );
            break;
    }
    if (is_null($next_id)) {
        continue;
    }
    $ilDB->insert('settings', [
            'module' => ['text', 'vedaimp'],
            'keyword' => ['text', $field_name],
            'value' => ['text', '' . $next_id]
    ]);
}

?>
<#15>
<?php
$query = "SELECT value FROM settings WHERE module = 'vedaclaiming' AND settings.keyword = 'fields'";
$result = $ilDB->query($query);
$remaining_md_field_names = [];

# migration of md field values
if (!is_null($row = $result->fetchAssoc())) {
    $map = unserialize($row['value']);
    foreach (['Ausbildungsgang-ID', 'Ausbildungszug-ID', 'Ausbildungsgangabschitt-ID', 'Ausbildungszugabschnitt-ID'] as $field_name) {
        if (isset($map[$field_name])) {
            $ilDB->insert('settings', [
                    'module' => ['text', 'vedaimp'],
                    'keyword' => ['text', $field_name],
                    'value' => ['text', '' . $map[$field_name]]
            ]);
        } else {
            $remaining_md_field_names[] = $field_name;
        }
    }
} else {
    $remaining_md_field_names = ['Ausbildungsgang-ID', 'Ausbildungszug-ID', 'Ausbildungsgangabschitt-ID', 'Ausbildungszugabschnitt-ID'];
}

$query = "SELECT * FROM settings WHERE  module = 'vedaimp' AND " . $ilDB->in('keyword', $remaining_md_field_names, false, ilDBConstants::T_TEXT);
$result = $ilDB->query($query);
while ($row = $result->fetchAssoc()) {
    $remaining_md_field_names = array_diff($remaining_md_field_names, [$row['keyword']]);
}

$record_ausbildung = null;
$query = "SELECT value FROM settings WHERE module = 'vedaimp' AND keyword = 'Sifa-Ausbildung'";
$result = $ilDB->query($query);
while ($row = $result->fetchAssoc()) {
    $record_ausbildung = (int) $row['value'];
}

$record_abschnitt = null;
$query = "SELECT value FROM settings WHERE module = 'vedaimp' AND keyword = 'Sifa-Abschnitt'";
$result = $ilDB->query($query);
while ($row = $result->fetchAssoc()) {
    $record_abschnitt = (int) $row['value'];
}

if (is_null($record_ausbildung) || is_null($record_abschnitt)) {
    throw new Exception("Sifa-Ausbildung, or Sifa-Abschnitt metadate record is missing.");
}

foreach ($remaining_md_field_names as $field_name) {
    $next_id = null;
    switch ($field_name) {
        case 'Ausbildungsgang-ID':
        case 'Ausbildungszug-ID':
            $next_id = ilAdvancedMDClaimingPlugin::createDBField(
                    $record_ausbildung,
                    ilAdvancedMDFieldDefinition::TYPE_TEXT,
                    $field_name,
                    null,
                    true
            );
            break;
        case 'Ausbildungsgangabschitt-ID':
        case 'Ausbildungszugabschnitt-ID':
            $next_id = ilAdvancedMDClaimingPlugin::createDBField(
                    $record_abschnitt,
                    ilAdvancedMDFieldDefinition::TYPE_TEXT,
                    $field_name,
                    null,
                    true
            );
            break;
    }
    if (is_null($next_id)) {
        continue;
    }
    $ilDB->insert('settings', [
            'module' => ['text', 'vedaimp'],
            'keyword' => ['text', $field_name],
            'value' => ['text', '' . $next_id]
    ]);
}
?>
<#16>
<?php
if (!$ilDB->tableExists('cron_crnhk_vedaimp_snd')) {
    $ilDB->createTable('cron_crnhk_vedaimp_snd',
            [
                    'seq_id' =>
                            [
                                    'type'		=> ilDBConstants::T_INTEGER,
                                    'length'	=> 8,
                                    'notnull'	=> true
                            ],
                    'crs_oid'	=>
                            [
                                    'type'		=> ilDBConstants::T_TEXT,
                                    'length'	=> 64,
                                    'notnull'	=> true
                            ],
                    'participant_oid'	=>
                            [
                                    'type'		=> ilDBConstants::T_TEXT,
                                    'length'	=> 64,
                                    'notnull'	=> true
                            ],
                    'status_passed'	=>
                            [
                                    'type'		=> ilDBConstants::T_INTEGER,
                                    'length'	=> 4,
                                    'notnull'	=> true
                            ],
                    'timestamp_passed' =>
                            [
                                    "type" => ilDBConstants::T_TIMESTAMP,
                                    "notnull" => false
                            ],
                    'status_send'	=>
                            [
                                    'type'		=> ilDBConstants::T_INTEGER,
                                    'length'	=> 4,
                                    'notnull'	=> true
                            ],
                    'timestamp_send' =>
                            [
                                    "type" => ilDBConstants::T_TIMESTAMP,
                                    "notnull" => false
                            ],
                    'error_code'	=>
                            [
                                    'type'		=> ilDBConstants::T_INTEGER,
                                    'length'	=> 4,
                                    'notnull'	=> true
                            ]
            ]
    );
    $ilDB->addPrimaryKey('cron_crnhk_vedaimp_snd',['seq_id']);
    $ilDB->createSequence('cron_crnhk_vedaimp_snd');
}
?>

<#16>
<?php
if ($ilDB->tableExists('cron_crnhk_vedaimp_snd')) {
    $ilDB->addTableColumn('cron_crnhk_vedaimp_snd', 'crs_id', [
            'type'		=> ilDBConstants::T_INTEGER,
            'length'	=> 8,
            'notnull'	=> true
    ]);
    $ilDB->addTableColumn('cron_crnhk_vedaimp_snd', 'crs_id', [
            'type'		=> ilDBConstants::T_INTEGER,
            'length'	=> 8,
            'notnull'	=> true
    ]);
}
?>
