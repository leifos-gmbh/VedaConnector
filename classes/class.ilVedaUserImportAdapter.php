<?php

use Swagger\Client\Model\TeilnehmerELearningPlattform;

/**
 * Class ilVedaUserImportAdapter
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilVedaUserImportAdapter
{
	private const AUTH_MODE = 'local';


	/**
	 * @var \ilLogger|null
	 */
	private $logger = null;

	/**
	 * @var \ilVedaConnectorSettings|null
	 */
	private $settings = null;

	/**
	 * @var \Swagger\Client\Model\TeilnehmerELearningPlattform[]
	 */
	private $participants = [];

	/**
	 * @var \ilXmlWriter|null
	 */
	private $writer = null;


	/**
	 * ilVedaUserImportAdapter constructor.
	 * @param TeilnehmerELearningPlattform[] $participants
	 */
	public function __construct(array $participants)
	{
		global $DIC;

		$this->logger = $DIC->logger()->vedaimp();
		$this->participants = $participants;
		$this->settings = \ilVedaConnectorSettings::getInstance();

		$this->writer = new \ilXmlWriter();
	}

	/**
	 * @return TeilnehmerELearningPlattform[]
	 */
	public function getParticipants()
	{
		return $this->participants;
	}


	/**
	 * Import users
	 * @throws \ilVedaUserImporterException
	 */
	public function import()
	{
		$this->transformParticipantsToXml();
		$this->importXml();
	}


	/**
	 * Transform API participants to xml
	 * @throws \ilVedaUserImporterException
	 */
	protected function transformParticipantsToXml()
	{
		$this->writer->xmlStartTag('Users');

		$this->logger->info('Starting update of ' . count($this->getParticipants()) . ' participants. ');
		foreach($this->getParticipants() as $participant_container)
		{
			$usr_id = $this->fetchUserId($participant_container);
			if($usr_id) {
				$this->writer->xmlStartTag(
					'User',
					[
						'Id' => $usr_id,
						'Action' => 'Update',
						'ImportId' => $participant_container->getTeilnehmer()->getOid()
					]);
				// @todo no login name update supported
			}
			else {
				$this->writer->xmlStartTag('User',
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
			}
			$this->writer->xmlElement(
				'Email',
				[],
				$participant_container->getEmail()
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
				],
				null
			);
			$this->writer->xmlElement(
				'Active',
				[],
				$participant_container->getTeilnehmer()->getAktiv() ? 'true' : 'false'
				);

			$this->writer->xmlElement('TimeLimitOwner',[],USER_FOLDER_ID);
			$this->writer->xmlElement('TimeLimitUnlimited',[],1);
			$this->writer->xmlElement('TimeLimitFrom',[],time());
			$this->writer->xmlElement('TimeLimitUntil',[],time());

			// Role assignment
			$long_role_id = ('il_' . IL_INST_ID . '_role_'.$this->settings->getParticipantRole());
			$this->writer->xmlElement(
				'Role',
				[
					'Id' => $long_role_id,
					'Type' => 'Global',
					'Action' => 'Assign'
				],
				null
			);

			$this->writer->xmlElement('Firstname', [], $participant_container->getTeilnehmer()->getVorname());
			$this->writer->xmlElement('Lastname',[], $participant_container->getTeilnehmer()->getNachname());

			$this->writer->xmlEndTag('User');
		}

		$this->writer->xmlEndTag('Users');
	}

	/**
	 *
	 */
	protected function importXml()
	{
		$this->logger->info('Starting user update');
		$importParser = new \ilUserImportParser();
		$importParser->setUserMappingMode(IL_USER_MAPPING_ID);
		$importParser->setXMLContent($this->writer->xmlDumpMem(false));
		$importParser->setRoleAssignment(
			[
				$this->settings->getParticipantRole() => $this->settings->getParticipantRole()
			]
		);
		$importParser->setFolderId(USER_FOLDER_ID);
		$importParser->startParsing();
		$debug = $importParser->getProtocol();

		$this->logger->info('Finished update users, with protocol message.');
		$this->logger->dump($debug);
		$this->logger->debug($this->writer->xmlDumpMem(true));
	}

	/**
	 * Fetch user id of already created user account
	 * @param \Swagger\Client\Model\TeilnehmerELearningPlattform $participant
	 * @return int
	 * @throws \ilVedaUserImporterException
	 */
	protected function fetchUserId(TeilnehmerELearningPlattform $participant)
	{
		$import_id = $participant->getTeilnehmer()->getOid();
		$obj_id = \ilObject::_lookupObjIdByImportId($import_id);
		if(!$obj_id)
		{
			return 0;
		}
		$user = \ilObjectFactory::getInstanceByObjId($obj_id, false);
		if(!$user instanceof \ilObjUser) {
			$this->logger->error('Found invalid obj_data entry for import_id: ' . $import_id);
			throw new \ilVedaUserImporterException('Invalid db structure. Check log file. Aborting');
		}
		return $user->getId();
	}


}