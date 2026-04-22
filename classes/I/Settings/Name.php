<?php

namespace Leifos\VedaConnector\I\Settings;

enum Name: string
{
    case HEADER_TOKEN = 'x-jwp-apiaccesstoken';
    case PERMANENT_SWITCH_ROLE = 'switch_permanent_role';
    case TEMPORARY_SWITCH_ROLE = 'switch_temporary_role';
    case LOCK = 'lock';
    case MAIL_ACTIVE = 'mail_active';
    case MAIL_TARGETS = 'mail_targets';
    case SIFA_ACTIVE = 'sifa_active';
    case STANDARD_ACTIVE = 'sibe_active';
    case CRON_INTERVAL = 'cron_interval';
    case CRON_LAST_EXECUTION = 'cron_last_execution';
    case REST_USER = 'restuser';
    case REST_URL = 'resturl';
    case SOAP_USER = 'soap_user';
    case SOAP_PASS = 'soap_pass';
    case REST_PASSWORD = 'restpassword';
    case REST_TOKEN = 'resttoken';
    case PLATTFORM_ID = 'platform_id';
    case ACTIVE = 'active';
    case LOGLEVEL = 'loglevel';
    case LOGFILE = 'logfile';
    case PART_ROLE = 'part_role';
    case STANDARD_PART_ROLE = 'sibe_part_role';
    case SIFA_IMPORT_REF_ID = 'sifa_import_ref_id';
    case STANDARD_IMPORT_REF_ID = 'sibe_import_ref_id';
    case TRAINING_COURSE = 'training_course';
    case ADD_HEADER_AUTH = 'add_header_auth';
    case ADD_HEADER_NAME = 'add_header_name';
    case ADD_HEADER_VALUE = 'add_header_value';
    case MD_FIELD_AUSBILDUNGSGANG = 'Ausbildungsgang-ID';
    case MD_FIELD_AUSBILDUNGSZUG = 'Ausbildungszug-ID';
    case MD_FIELD_AUSBILDUNGSGANGABSCHNITT = 'Ausbildungsgangabschitt-ID';
    case MD_FIELD_AUSBILDUNGSZUGABSCHNITT = 'Ausbildungszugabschnitt-ID';
}
