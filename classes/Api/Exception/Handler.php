<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Exception;

use Exception;
use ilLogLevel;
use Leifos\VedaConnector\GeneratedOpenApi\ApiException;
use Leifos\VedaConnector\I\Api\Exception\HandlerInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type as MailSegmentType;
use Leifos\VedaConnector\I\Mail\DB\FactoryInterface as MailDBFactoryInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;
use Throwable;

class Handler implements HandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected MailDBFactoryInterface $mail_db_factory
    ) {
    }

    public function writeToLog(
        Exception $e,
        string $api_call_name,
        string $access_token
    ): void {
        $this->logger->warning(SettingsName::HEADER_TOKEN->value . ': ' . $access_token);
        $this->logger->warning($api_call_name . ' failed with message: ' . $e->getMessage());
        if ($e instanceof ApiException) {
            $this->logger->dump($e->getResponseHeaders() ?? [], ilLogLevel::WARNING);
            $this->logger->dump($e->getTraceAsString(), ilLogLevel::WARNING);
            $this->logger->warning($e->getResponseBody() ?? "");
        }
        if (!($e instanceof ApiException)) {
            $this->logger->dump($e->getTraceAsString(), ilLogLevel::WARNING);
        }
    }

    public function storeAsMailSegment(
        Exception $e,
        string $api_call_name,
        string $access_token
    ): void {
        $this->mail_db_factory->elementBuilder()
            ->withType(MailSegmentType::ERROR)
            ->withMessage('Verbindungsfehler beim Aufuf von: ' . $api_call_name)
            ->store();
    }
}
