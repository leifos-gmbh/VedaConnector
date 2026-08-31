<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector;

use ilLogLevel;
use Leifos\VedaConnector\I\Api\HandlerInterface as ApiInterface;
use Leifos\VedaConnector\I\Exception\FactoryInterface as ExceptionFactoryInterface;
use Leifos\VedaConnector\I\Exception\Message;
use Leifos\VedaConnector\I\ImporterInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerHandlerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name;

class Importer implements ImporterInterface
{
    public function __construct(
        protected LoggerHandlerInterface $logger,
        protected SettingsInterface $settings,
        protected ApiInterface $api,
        protected ExceptionFactoryInterface $exception_factory
    ) {
    }

    public function import(
        int $import_type,
        bool $all,
        array $types = []
    ): void {
        $modes = $this->getImportModes($all, $types);
        if ($this->settings->readAsBool(Name::LOCK)) {
            throw $this->exception_factory->importerLocked(Message::ERR_IMPORT_LOCKED);
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

    protected function importSifa(array $modes) : void
    {
        $this->ensureUDFAndMDFieldsAreSetup();
        if ($this->isImportModeEnabled(self::IMPORT_USR_ALL, $modes)) {
            $this->logger->info('Importing all users');
            $this->api->deleteDeprecatedILIASUsers();
            $this->api->importILIASUsersSIFA(false);
        }
        if ($this->isImportModeEnabled(self::IMPORT_USR_INCREMENTAL, $modes)) {
            $this->logger->info('Importing new users');
            $this->api->importILIASUsersSIFA(true);
        }
        if ($this->isImportModeEnabled(self::IMPORT_CRS, $modes)) {
            $this->logger->debug('Importing courses');
            $this->api->importSIFACourses();
        }
        if ($this->isImportModeEnabled(self::IMPORT_MEM, $modes)) {
            $this->logger->debug('Importing memberships');
            $this->api->importSIFAMembers();
        }
    }

    protected function importStandard(array $modes) : void
    {
        $this->logger->dump($modes, ilLogLevel::DEBUG);
        if ($this->isImportModeEnabled(self::IMPORT_USR_ALL, $modes)) {
            $this->logger->info('Importing all users');
            $this->api->deleteDeprecatedILIASUsers();
            $this->api->importILIASUsersStandard(false);
        }
        if ($this->isImportModeEnabled(self::IMPORT_USR_INCREMENTAL, $modes)) {
            $this->logger->info('Importing new users');
            $this->api->importILIASUsersStandard(true);
        }
        if ($this->isImportModeEnabled(self::IMPORT_CRS, $modes)) {
            $this->logger->debug('Importing courses');
            $this->api->importStandardCourses();
        }
        if ($this->isImportModeEnabled(self::IMPORT_MEM, $modes)) {
            $this->logger->debug('Importing memeberships');
            $this->api->importStandardMembers();
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

    protected function ensureUDFAndMDFieldsAreSetup() : void
    {
        if (!$this->settings->mdFieldsAvailable()) {
            throw $this->exception_factory->claimingMissing(Message::ERR_MISSING);
        }
        if (!$this->settings->udfFieldsAvailable()) {
            throw $this->exception_factory->claimingMissing(Message::ERR_MISSING_UDF);
        }
    }
}
