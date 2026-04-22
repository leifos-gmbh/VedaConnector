<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace Leifos\VedaConnector;

use ilLogLevel;
use Handler;
use ilVedaClaimingMissingException;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name;
use ilVedaImporterLockedException;
use Leifos\VedaConnector\I\ImporterInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerHandlerInterface;

class Importer implements ImporterInterface
{
    public function __construct(
        protected LoggerHandlerInterface $logger,
        protected SettingsInterface $settings,
        protected ilVedaConnectorPlugin $plugin,
        protected Handler $my_api
    ) {
    }

    /**
     * @throws ilVedaImporterLockedException
     * @throws ilVedaClaimingMissingException
     */
    public function import(
        int $import_type,
        bool $all,
        array $types = []
    ): void {
        $modes = $this->getImportModes($all, $types);
        if ($this->settings->readAsBool(Name::LOCK)) {
            throw new ilVedaImporterLockedException(
                $this->plugin->txt('error_import_locked')
            );
        }
        $this->logger->info('Settings import lock');
        $this->settings->writeBool(Name::LOCK, true);

        if (
            $import_type === ImporterInterface::IMPORT_TYPE_SIFA ||
            (
                $import_type === self::IMPORT_TYPE_UNDEFINED &&
                $this->settings->readAsBool(Name::SIFA_ACTIVE)
            )
        ) {
            $this->logger->debug('SIFA import startet.');
            $this->importSifa($modes);
        }
        if (
            $import_type === ImporterInterface::IMPORT_TYPE_STANDARD ||
            (
                $import_type === self::IMPORT_TYPE_UNDEFINED &&
                $this->settings->readAsBool(Name::STANDARD_ACTIVE)
            )
        ) {
            $this->logger->debug('Standard import started.');
            $this->importStandard($modes);
        }

        // no error release lock
        $this->logger->info('Releasing import lock');
        $this->settings->writeBool(Name::LOCK, false);
    }

    /**
     * @throws ilVedaClaimingMissingException
     */
    protected function importSifa(array $modes) : void
    {
        $this->ensureClaimingPluginConfigured();
        if ($this->isImportModeEnabled(self::IMPORT_USR_ALL, $modes)) {
            $this->logger->info('Importing all users');
            $this->my_api->deleteDeprecatedILIASUsers();
            $this->my_api->importILIASUsersSIFA(false);
        }
        if ($this->isImportModeEnabled(self::IMPORT_USR_INCREMENTAL, $modes)) {
            $this->logger->info('Importing new users');
            $this->my_api->importILIASUsersSIFA(true);
        }
        if ($this->isImportModeEnabled(self::IMPORT_CRS, $modes)) {
            $this->logger->debug('Importing courses');
            $this->my_api->importSIFACourses();
        }
        if ($this->isImportModeEnabled(self::IMPORT_MEM, $modes)) {
            $this->logger->debug('Importing memberships');
            $this->my_api->importSIFAMembers();
        }
    }

    protected function importStandard(array $modes) : void
    {
        $this->logger->dump($modes, ilLogLevel::DEBUG);
        if ($this->isImportModeEnabled(self::IMPORT_USR_ALL, $modes)) {
            $this->logger->info('Importing all users');
            $this->my_api->deleteDeprecatedILIASUsers();
            $this->my_api->importILIASUsersStandard(false);
        }
        if ($this->isImportModeEnabled(self::IMPORT_USR_INCREMENTAL, $modes)) {
            $this->logger->info('Importing new users');
            $this->my_api->importILIASUsersStandard(true);
        }
        if ($this->isImportModeEnabled(self::IMPORT_CRS, $modes)) {
            $this->logger->debug('Importing courses');
            $this->my_api->importStandardCourses();
        }
        if ($this->isImportModeEnabled(self::IMPORT_MEM, $modes)) {
            $this->logger->debug('Importing memeberships');
            $this->my_api->importStandardMembers();
        }
    }

    protected function getImportModes(bool $all, array $types = []) : array
    {
        return $all ?
            [
                self::IMPORT_USR_INCREMENTAL,
                self::IMPORT_CRS,
                self::IMPORT_MEM,
            ]
            : $types;
    }

    protected function isImportModeEnabled(string $mode, array $modes) : bool
    {
        return in_array($mode, $modes);
    }

    /**
     * @throws ilVedaClaimingMissingException
     */
    protected function ensureClaimingPluginConfigured() : void
    {
        if (!$this->plugin->isClaimingPluginAvailable()) {
            throw new ilVedaClaimingMissingException('', ilVedaClaimingMissingException::ERR_MISSING);
        }
        if (!$this->plugin->isUDFClaimingPluginAvailable()) {
            throw new ilVedaClaimingMissingException('', ilVedaClaimingMissingException::ERR_MISSING_UDF);
        }
    }
}
