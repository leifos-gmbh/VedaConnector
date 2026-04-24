<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Exception;

use ilException;
use Leifos\VedaConnector\I\Exception\FactoryInterface;
use Leifos\VedaConnector\I\Exception\Message;
use Leifos\VedaConnector\I\PluginInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected PluginInterface $plugin
    ) {
    }

    protected function translateMessage(
        Message $message
    ) : string {
        return $message == Message::NULL ? '' : $this->plugin->txt($message->value);
    }

    public function claimingMissing(
        Message $message
    ): ilException {
        $code = $message == Message::ERR_MISSING ? 1 : 0;
        $code = $message == Message::ERR_MISSING_UDF ? 2: $code;
        return new ClaimingMissing($this->translateMessage($message), $code);
    }

    public function connection(
        Message $message
    ): ilException {
        return new Connection('soap_connection_failed' . $this->translateMessage($message), 3);
    }

    public function courseImporter(
        Message $message
    ): ilException {
        return new CourseImporter($this->translateMessage($message));
    }

    public function importerLocked(
        Message $message
    ): ilException {
        return new ImporterLocked($this->translateMessage($message));
    }

    public function memberImport(
        Message $message,
        string $additional_message = ''
    ): ilException {
        return new MemberImport($this->translateMessage($message) . $additional_message);
    }

    public function userImport(
        Message $message,
        string $additional_message = ''
    ): ilException {
        return new UserImport($this->translateMessage($message) . $additional_message);
    }
}
