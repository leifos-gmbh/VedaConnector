<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail;

use Exception;
use ilMailMimeSenderFactory;
use ilMimeMail;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerHandlerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\CollectionInterface as MailDBElementCollectionInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type;
use Leifos\VedaConnector\I\Mail\DB\HandlerInterface as MailDBInterface;
use Leifos\VedaConnector\I\Mail\HandlerInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name;

class Handler implements HandlerInterface
{
    public function __construct(
        protected ilMailMimeSenderFactory $sender_factory,
        protected MailDBInterface $mail_db,
        protected LoggerHandlerInterface $logger,
        protected SettingsInterface $settings
    ) {
    }

    /**
     * @throws Exception
     */
    public function sendStatus() : void
    {
        $this->logger->debug('Sending Status Mail');
        $mail_segments = $this->mail_db->lookupAll();
        $mail_segments_errors = $mail_segments->getSubCollectionByType(Type::ERROR);
        $body = '';
        $subject = '';
        if (count($mail_segments_errors) === 0) {
            $this->logger->debug('Status Mail NOT send, no errors to report');
            return;
        }
        if (count($mail_segments_errors) > 0) {
            $body .= 'Während der Aktualisierung sind Fehler aufgetreten:' . "\n\n";
            $body = $this->addSegmentMessagesToBody($body, $mail_segments_errors);
            $body .= "\n";
            $subject = 'FEHLER ilVedaConnectorPlugin import';
        }
        $body = $this->addImportInfoToBody($body, $mail_segments);
        $body .= "\n" . 'Diese E-Mail wurde automatisch generiert.';
        $this->send($subject, $body);
        $this->clearMailData();
    }

    /**
     * @throws Exception
     */
    public function sendSIFACourseCompleted(): void
    {
        $this->logger->debug('Sending Mail');
        $mail_segments = $this->mail_db->lookupAll();
        $mail_segments_errors = $mail_segments->getSubCollectionByType(Type::ERROR);
        $body = '';
        $subject = '';
        if (count($mail_segments_errors) === 0) {
            $body .= 'SIFA Kurs erfolgreich importiert!' . "\n\n";
            $subject = 'ERFOLG ilVedaConnectorPlugin SIFA Kurs import';
        }
        if (count($mail_segments_errors) > 0) {
            $body .= 'Während der Aktualisierung sind Fehler aufgetreten:' . "\n\n";
            $body = $this->addSegmentMessagesToBody($body, $mail_segments_errors);
            $body .= "\n";
            $subject = 'FEHLER ilVedaConnectorPlugin SIFA Kurs import';
        }
        $body = $this->addImportInfoToBody($body, $mail_segments);
        $body .= "\n" . 'Diese E-Mail wurde automatisch generiert.';
        $this->send($subject, $body);
        $this->clearMailData();
    }

    protected function send(string $subject, string $body) : void
    {
        $mmail = new ilMimeMail();
        $mmail->From($this->sender_factory->system());
        $mmail->Subject($subject, true);
        $mmail->To($this->settings->read(Name::MAIL_TARGETS));
        $mmail->Body($body);

        $this->logger->debug("\n" . $body);
        $this->dumpMail($mmail);
        $mmail->Send();
    }

    protected function addSegmentMessagesToBody(string $body, MailDBElementCollectionInterface $elements) : string
    {
        foreach ($elements as $element) {
            $body .= $element->getMessage() . "\n";
        }
        return $body;
    }

    protected function addImportInfoToBody(string $body, MailDBElementCollectionInterface $elements) : string
    {
        $segments_user_updated = $elements->getSubCollectionByType(Type::USER_UPDATED);
        $body .= 'Anzahl Aktualisierungen von Nutzerkonten: ' . count($segments_user_updated) . "\n";

        $segments_user_updated = $elements->getSubCollectionByType(Type::USER_IMPORTED);
        $body .= 'Anzahl neu importierter Nutzerkonten: ' . count($segments_user_updated) . "\n";

        $segments_courses_updated = $elements->getSubCollectionByType(Type::COURSE_UPDATED);
        $body .= 'Anzahl neu importierter Kurse: ' . count($segments_courses_updated) . "\n";

        $segments_mmbrshp_updated = $elements->getSubCollectionByType(Type::MEMBERSHIP_UPDATED);
        $body .= 'Anzahl Aktualisierungen von Mitgliedschaften: ' . count($segments_mmbrshp_updated) . "\n";

        return $body;
    }

    protected function clearMailData() : void
    {
        $this->mail_db->deleteAll();
    }

    protected function dumpMail(
        ilMimeMail $mail
    ) : void {
        $this->logger->debug('From:' . $mail->getFrom()->getFromAddress());
        $this->logger->debug('Subject:' . $mail->getSubject());
        $this->logger->debug('To: ' . implode(',', $mail->getTo()));
        $this->logger->debug('Body:' . $mail->getFinalBody());
    }
}
