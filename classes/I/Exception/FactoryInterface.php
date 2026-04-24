<?php

namespace Leifos\VedaConnector\I\Exception;

use ilException;

interface FactoryInterface
{
    public function claimingMissing(
        Message $message
    ): ilException;

    public function connection(
        Message $message
    ): ilException;

    public function courseImporter(
        Message $message
    ): ilException;

    public function importerLocked(
        Message $message
    ): ilException;

    public function memberImport(
        Message $message,
        string $additional_message = ''
    ): ilException;

    public function userImport(
        Message $message,
        string $additional_message = ''
    ): ilException;
}
