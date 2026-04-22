<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Adapter;

use DateTime;
use ilAuthUtils;
use ilDate;
use ilDateTime;
use ilLogLevel;
use ilObject;
use ilObjectFactory;
use ilObjectNotFoundException;
use ilObjUser;
use ilStr;
use ilUserImportParser;
use ilVedaUserImporterException;
use ilXmlWriter;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Adresse;
use Leifos\VedaConnector\GeneratedOpenApi\Model\Organisation;
use Leifos\VedaConnector\GeneratedOpenApi\Model\TeilnehmerELearningPlattform;
use Leifos\VedaConnector\I\Api\Adapter\UserImportInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\Collections\FactoryInterface as ELearningPlattformCollectionsFactoryInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\Collections\ParticipantsInterface;
use Leifos\VedaConnector\I\Api\ElearningPlattform\HandlerInterface as ELearningPlattformApiInterface;
use Leifos\VedaConnector\I\Api\Organisation\HandlerInterface as OrganisationApiInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type as MailType;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status as UserStatusStatus;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface as UserStatusDBInterface;

class UserImport implements UserImportInterface
{
    public const IMPORT_MODE_STANDARD = 0;
    public const IMPORT_MODE_SIFA = 1;
    protected const AUTH_MODE = 'local';
    protected const ERR_LOGIN_EXIST_MSG = 'Ein ILIAS-Benutzerkonto mit dem Namen %s existiert bereits.';

    /**
     * @var Organisation[]
     */
    protected array $organisations = [];
    protected ilXmlWriter $writer;

    public function __construct(
        protected LoggerInterface $logger,
        protected SettingsInterface $settings,
        protected UserStatusDBInterface $user_status_db,
        protected BuilderFactoryInterface $builder_factory,
        protected ELearningPlattformCollectionsFactoryInterface $elearning_plattform_collections_factory,
        protected ELearningPlattformApiInterface $elearning_plattform_api,
        protected OrganisationApiInterface $organisation_api,
    ) {
        $this->writer = new ilXmlWriter();
    }

    public function import(
        ParticipantsInterface $participants,
        int $import_mode
    ) : void {
        // this is a hack to send maximum 50 participants via soap
        $num = 0;
        $sequenced_participants = [];
        foreach ($participants as $idx => $participant) {
            if (++$num >= 50) {
                $participant_collection = $this->elearning_plattform_collections_factory->participants(...$sequenced_participants);
                $this->transformParticipantsToXml($participant_collection, $import_mode);
                $this->importXml($import_mode);
                $this->updateCreationFeedback();
                $num = 0;
                $sequenced_participants = [];
            }
            $sequenced_participants[] = $participant;
        }
        $participant_collection = $this->elearning_plattform_collections_factory->participants(...$sequenced_participants);
        $this->transformParticipantsToXml($participants, $import_mode);
        $this->importXml($import_mode);
        $this->updateCreationFeedback();
    }

    protected function transformParticipantsToXml(
        ParticipantsInterface $participants,
        int $import_mode
    ) : void {
        $this->writer = new ilXmlWriter();
        $this->writer->xmlStartTag('Users');

        $this->logger->info('Starting update of ' . count($participants) . ' participants. ');
        foreach ($participants as $participant_container) {
            $usr_id = $this->fetchUserId($participant_container);

            if (!$this->validateParticipant($usr_id, $participant_container)) {
                continue;
            }

            $user = null;
            if ($usr_id) {
                try {
                    $user = ilObjectFactory::getInstanceByObjId($usr_id);
                } catch (ilObjectNotFoundException $e) {
                    $this->logger->warning('Cannot create user instance for: ' . $usr_id);
                    continue;
                }

                $new_login = '';
                if (!$this->updateLogin($usr_id, $participant_container, $new_login)) {
                    continue;
                }

                $this->writer->xmlStartTag(
                    'User',
                    [
                        'Id' => $usr_id,
                        'Action' => 'Update',
                        'ImportId' => $participant_container->getTeilnehmer()->getOid()
                    ]
                );

                $this->writer->xmlElement(
                    'Login',
                    [],
                    $participant_container->getBenutzername()
                );

                if ($user instanceof ilObjUser && !$this->hasPasswordChanged($user)) {
                    $this->writer->xmlElement(
                        'Password',
                        [
                            'Type' => 'PLAIN'
                        ],
                        $participant_container->getInitialesPasswort()
                    );
                }
                if ($user instanceof ilObjUser && $this->isGenderEmpty($user)) {
                    $this->writer->xmlElement(
                        'Gender',
                        [],
                        strtolower($participant_container->getTeilnehmer()->getGeschlecht())
                    );
                }
            } else {
                $this->writer->xmlStartTag(
                    'User',
                    [
                        'Action' => 'Insert',
                        'ImportId' => $participant_container->getTeilnehmer()->getOid()
                    ]
                );
                $this->writer->xmlElement(
                    'Login',
                    [],
                    $participant_container->getBenutzername()
                );
                $this->writer->xmlElement(
                    'Password',
                    [
                        'Type' => 'PLAIN'
                    ],
                    $participant_container->getInitialesPasswort()
                );
                $this->writer->xmlElement(
                    'Gender',
                    [],
                    strtolower($participant_container->getTeilnehmer()->getGeschlecht())
                );
                $this->writer->xmlElement(
                    'AuthMode',
                    [
                        'type' => self::AUTH_MODE
                    ]
                );

            }
            $this->writer->xmlElement(
                'Email',
                [],
                $participant_container->getEmail()
            );


            if ($participant_container->getTeilnehmer()->getGeburtsdatum() instanceof DateTime) {
                $date_string = $participant_container->getTeilnehmer()->getGeburtsdatum()->format('Y-m-d');
                if ($date_string) {
                    $this->writer->xmlElement(
                        'Birthday',
                        [],
                        $date_string
                    );
                }
            }

            $this->writer->xmlElement(
                'Active',
                [],
                (
                    $this->isValidDate($participant_container->getGueltigAb(), $participant_container->getGueltigBis()) &&
                    $participant_container->getTeilnehmer()->getAktiv()
                )
                    ? 'true'
                    : 'false'
            );
            $this->updateTimeLimit($participant_container, $user);


            // Role assignment
            $participant_role = ($import_mode === self::IMPORT_MODE_SIFA)
                ? $this->settings->readAsInt(SettingsName::PART_ROLE)
                : $this->settings->readAsInt(SettingsName::STANDARD_PART_ROLE);
            $long_role_id = ('il_' . IL_INST_ID . '_role_' . $participant_role);
            $this->writer->xmlElement(
                'Role',
                [
                    'Id' => $long_role_id,
                    'Type' => 'Global',
                    'Action' => 'Assign'
                ]
            );

            $this->writer->xmlElement('Firstname', [], $participant_container->getTeilnehmer()->getVorname());
            $this->writer->xmlElement('Lastname', [], $participant_container->getTeilnehmer()->getNachname());

            $this->parseOrganisationInfo($participant_container->getTeilnehmer()->getGeschaeftlichOrganisationId());

            $this->writer->xmlEndTag('User');

            $this->storeUserStatusSuccess($participant_container, $usr_id);
        }

        $this->writer->xmlEndTag('Users');
    }

    protected function updateTimeLimit(
        TeilnehmerELearningPlattform $participant,
        ilObjUser $user = null
    ) : void {
        $start = $end = 0;
        if ($participant->getGueltigAb() instanceof DateTime) {
            $start = $participant->getGueltigAb()->getTimestamp();
        }
        if ($participant->getGueltigBis() instanceof DateTime) {
            $end = $participant->getGueltigBis()->getTimestamp();
        }
        $this->writer->xmlElement('TimeLimitOwner', [], USER_FOLDER_ID);
        if (!$start || !$end) {
            $this->writer->xmlElement('TimeLimitUnlimited', [], 1);
        } else {
            $this->writer->xmlElement('TimeLimitUnlimited', [], 0);
        }
        if ($start && $end) {
            $this->writer->xmlElement('TimeLimitFrom', [], $start);
            $this->writer->xmlElement('TimeLimitUntil', [], $end);
        } else {
            $this->writer->xmlElement('TimeLimitFrom', [], 0);
            $this->writer->xmlElement('TimeLimitUntil', [], 0);
        }
    }

    protected function importXml(int $import_mode) : void
    {
        $participant_role = ($import_mode === self::IMPORT_MODE_SIFA)
            ? $this->settings->readAsInt(SettingsName::PART_ROLE)
            : $this->settings->readAsInt(SettingsName::STANDARD_PART_ROLE);
        $this->logger->info('Starting user update');
        $importParser = new ilUserImportParser();
        $importParser->setUserMappingMode(ilUserImportParser::IL_USER_MAPPING_ID);
        $importParser->setXMLContent($this->writer->xmlDumpMem(false));
        $importParser->setRoleAssignment(
            [
                $participant_role => $participant_role
            ]
        );
        $importParser->setFolderId(USER_FOLDER_ID);
        $importParser->startParsing();
        $debug = $importParser->getProtocol();

        $message = 'Finished update users, with protocol message.';
        $this->logger->info($message);
        $this->logger->dump($debug, ilLogLevel::DEBUG);
        $this->logger->debug($this->writer->xmlDumpMem());
    }

    protected function updateCreationFeedback() : void
    {
        $pending_participants = $this->user_status_db->lookupAll()->getSubCollectionOfElementsWithPendingStatus();
        foreach ($pending_participants as $participant_status) {
            if ($this->elearning_plattform_api->sendAccountCreated($participant_status->getOid())) {
                $this->logger->debug('Marked user with oid ' . $participant_status->getOid() . ' as imported.');
                $this->logger->info('Update creation status');
                $this->user_status_db->update($participant_status->withCreationStatus(UserStatusStatus::SYNCHRONIZED));
            }
        }
    }

    protected function fetchUserId(
        TeilnehmerELearningPlattform $participant
    ) : int {
        $import_id = $participant->getTeilnehmer()->getOid();
        $obj_id = ilObject::_lookupObjIdByImportId($import_id);
        if (!$obj_id) {
            return 0;
        }
        if (!ilObjUser::_exists($obj_id)) {
            $this->logger->error('Found invalid obj_data entry for import_id: ' . $import_id);
            throw new ilVedaUserImporterException('Invalid db structure. Check log file. Aborting');
        }
        $user = ilObjectFactory::getInstanceByObjId($obj_id, false);
        if (!$user instanceof ilObjUser) {
            $this->logger->error('Found invalid obj_data entry for import_id: ' . $import_id);
            throw new ilVedaUserImporterException('Invalid db structure. Check log file. Aborting');
        }
        return $user->getId();
    }

    protected function validateParticipant(
        int $usr_id,
        TeilnehmerELearningPlattform $participant
    ) : bool {
        if ($usr_id) {
            $this->logger->debug('Existing usr_account with id: ' . $usr_id . ' is valid');
            return true;
        }

        if (!$this->isValidDate($participant->getGueltigAb(), $participant->getGueltigBis())) {
            $this->logger->info('Ignoring participant outside valid time.');
            return false;
        }

        // no usr_id given => usr is valid if login does not exist
        $login = $participant->getBenutzername();
        $generated_login = ilAuthUtils::_generateLogin($login);

        if (strcasecmp($generated_login, $login) !== 0) {
            $message = 'User with login: ' . $login . ' already exists.';
            $this->logger->warning($message);
            $this->builder_factory->mailSegment()
                ->withType(MailType::ERROR)
                ->withMessage($message)
                ->store();
            $this->elearning_plattform_api->sendAccountCreationFailed(
                $participant->getTeilnehmer()->getOid(),
                sprintf(self::ERR_LOGIN_EXIST_MSG, $login)
            );
            $this->builder_factory->userStatus()
                ->withOID($participant->getTeilnehmer()->getOid())
                ->withLogin($participant->getBenutzername())
                ->withCreationStatus(UserStatusStatus::NONE)
                ->withPasswordStatus(UserStatusStatus::NONE)
                ->withImportFailure(true)
                ->store();
            return false;
        }
        return true;
    }

    protected function updateLogin(
        int $usr_id,
        TeilnehmerELearningPlattform $participant,
        string &$new_login
    ) : bool {
        $user = ilObjectFactory::getInstanceByObjId($usr_id, false);
        if (!$user instanceof ilObjUser) {
            $this->logger->warning('Cannot find existing user with id: ' . $usr_id);
            return false;
        }
        $login = $participant->getBenutzername();
        if (strcasecmp($login, $user->getLogin()) === 0) {
            $this->logger->debug('User login name unchanged.');
            $new_login = $login;
            return true;
        }
        $generated_login = ilAuthUtils::_generateLogin($login);
        if (strcasecmp($generated_login, $login) !== 0) {
            $message = 'User with login: ' . $login . ' already exists.';
            $this->logger->warning($message);
            $this->builder_factory->mailSegment()
                ->withMessage($message)
                ->withType(MailType::ERROR)
                ->store();
            $this->elearning_plattform_api->sendAccountCreationFailed(
                $participant->getTeilnehmer()->getOid(),
                sprintf(self::ERR_LOGIN_EXIST_MSG, $login)
            );
            $this->builder_factory->userStatus()
                ->withOID($participant->getTeilnehmer()->getOid())
                ->withLogin($participant->getBenutzername())
                ->withImportFailure(true)
                ->store();
            return false;
        }
        $new_login = $generated_login;
        return true;
    }

    protected function hasPasswordChanged(
        ilObjUser $user
    ) : bool {
        return $user->getLastPasswordChangeTS() > 0;
    }

    protected function isGenderEmpty(
        ilObjUser $user
    ) : bool {
        return $user->getGender() == '';
    }

    protected function storeUserStatusSuccess(
        TeilnehmerELearningPlattform $participant,
        int $usr_id
    ) : void {
        if (!$usr_id) {
            $this->builder_factory->userStatus()
                ->withOID($participant->getTeilnehmer()->getOid())
                ->withLogin($participant->getBenutzername())
                ->withPasswordStatus(UserStatusStatus::NONE)
                ->withCreationStatus(UserStatusStatus::NONE)
                ->withImportFailure(false)
                ->store();
            $this->builder_factory->mailSegment()
                ->withType(MailType::USER_IMPORTED)
                ->withMessage('Imported user with oid: ' . $participant->getTeilnehmer()->getOid())
                ->store();
        }
        if ($usr_id) {
            $this->builder_factory->userStatus()
                ->withOID($participant->getTeilnehmer()->getOid())
                ->withLogin($participant->getBenutzername())
                ->withImportFailure(false)
                ->store();
            $this->builder_factory->mailSegment()
                ->withType(MailType::USER_UPDATED)
                ->withMessage('Updated user with oid: ' . $participant->getTeilnehmer()->getOid())
                ->store();
        }
    }

    protected function parseOrganisationInfo(?string $orgoid) : bool
    {
        if (!$orgoid) {
            return false;
        }
        if (isset($this->organisations[$orgoid])) {
            $this->writeOrganisationInfo($this->organisations[$orgoid]);
            return true;
        }
        $org = $this->organisation_api->getOrganisation($orgoid);
        if (!is_null($org)) {
            $this->organisations[$orgoid] = $org;
            $this->writeOrganisationInfo($this->organisations[$orgoid]);
        }
        if (is_null($org)) {
            $this->logger->warning('Cannot read organisation info for org oid: ' . $orgoid);
        }
        return true;
    }

    protected function writeOrganisationInfo(Organisation $org) : void
    {
        $this->logger->dump($org, ilLogLevel::DEBUG);

        $org_parts = [];
        if (strlen(trim((string) $org->getOrganisationsname1()))) {
            $org_parts[] = (string) $org->getOrganisationsname1();
        }
        if (strlen(trim((string) $org->getOrganisationsname2()))) {
            $org_parts[] = (string) $org->getOrganisationsname2();
        }
        if (strlen(trim((string) $org->getOrganisationsname3()))) {
            $org_parts[] = (string) $org->getOrganisationsname3();
        }

        if (count($org_parts)) {
            $this->writer->xmlElement('Institution', [], ilStr::shortenText(implode(' ', $org_parts), 0, 70));
        }

        if ($org->getAdresse() instanceof Adresse) {
            $city = $org->getAdresse()->getOrt();
            if (strlen($city)) {
                $this->writer->xmlElement('City', null, $city);
            }
        }
        $plugin = ilVedaConnectorPlugin::getInstance();
        if (!$plugin->isUDFClaimingPluginAvailable()) {
            $this->logger->warning('Import of organisation information failed: no udf plugin found');
            return;
        }
        $udfclaiming = $plugin->getUDFClaimingPlugin();
        foreach ($udfclaiming->getFields() as $field_name => $field_id) {
            $value = '';
            $field_update_required = false;
            switch ($field_name) {

                case VedaUDFClaimingFields::SUPERVISOR->value:
                    $field_update_required = true;
                    $value = $org->getAufsichtspersonName();
                    break;

                case VedaUDFClaimingFields::SUPERVISOR_EMAIL->value:
                    $field_update_required = true;
                    $value = $org->getAufsichtspersonEMail();
                    break;

                case VedaUDFClaimingFields::MEMBER_ID->value:
                    $field_update_required = true;
                    $value = $org->getMitgliedsnummer();
                    break;
            }
            if ($field_update_required) {
                $this->writer->xmlElement(
                    'UserDefinedField',
                    [
                        'Id' => 'il_' . IL_INST_ID . '_udf_' . $field_id,
                        'Name' => 'unknown'
                    ],
                    $value
                );
            }
        }
    }

    protected function isValidDate(?DateTime $start, ?DateTime $end) : bool
    {
        if ($start == null) {
            return true;
        }
        $now = new ilDate(time(), IL_CAL_UNIX);
        $ilstart = new ilDate($start->format('Y-m-d'), IL_CAL_DATE);

        if ($end == null) {

            // check starting time <= now
            if (
                ilDateTime::_before($ilstart, $now, IL_CAL_DAY)
                || ilDateTime::_equals($ilstart, $now, IL_CAL_DAY)
            ) {
                $this->logger->debug('Starting date is valid');
                return true;
            }
            $this->logger->debug('Starting date is invalid');
            return false;
        }

        $ilend = new ilDate($end->format('Y-m-d'), IL_CAL_DATE);

        if (
            ilDateTime::_within(
                $now,
                $ilstart,
                $ilend,
                IL_CAL_DAY
            )
        ) {
            return true;
        }
        return false;
    }
}
