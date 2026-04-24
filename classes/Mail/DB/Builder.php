<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB;

use Exception;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Mail\DB\BuilderInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface as EntryFactoryInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface as EntryHandlerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type;
use Leifos\VedaConnector\I\Mail\DB\HandlerInterface as MailDBInterface;

class Builder implements BuilderInterface
{
    protected EntryHandlerInterface $element;

    /**
     * @throws Exception
     */
    public function __construct(
        protected MailDBInterface $mail_db,
        protected LoggerInterface $logger,
        protected EntryFactoryInterface $entry_factory
    ) {
        $this->element = $this->entry_factory->handler(-1)
            ->withType(Type::NONE)
            ->withMessage('');
    }

    public function withType(
        Type $type
    ) : BuilderInterface {
        $this->logger->debug('Adding type: "' . $type->name . '", to mail segment with id: ' . $this->element->getId());
        $clone = clone $this;
        $clone->element = $this->element->withType($type);
        return $clone;
    }

    public function withMessage(
        string $message
    ) : BuilderInterface {
        $this->logger->debug('Adding message: "' . $message . '", to mail segment with id: ' . $this->element->getId());
        $clone = clone $this;
        $clone->element = $this->element->withMessage($message);
        return $clone;
    }

    public function store() : void
    {
        $this->logger->debug('Storing mail segment with id: ' . $this->element->getId());
        $this->mail_db->write($this->element);
    }
}
