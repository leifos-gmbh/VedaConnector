<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Mail\DB\Element;

use DateTimeImmutable;
use DateTimeZone;
use Leifos\VedaConnector\I\Mail\DB\Element\HandlerInterface;
use Leifos\VedaConnector\I\Mail\DB\Element\Type;

class Handler implements HandlerInterface
{
    protected string $message;
    protected Type $type;
    protected DateTimeImmutable $modified;

    public function __construct(
        protected int $id
    ) {
        $this->id = $id;
        $this->message = '';
        $this->type = Type::NONE;
        $this->modified = new DateTimeImmutable('now', new DateTimeZone('Utc'));
    }

    public function getID() : int
    {
        return $this->id;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function getType() : Type
    {
        return $this->type;
    }

    public function getLastModified() : DateTimeImmutable
    {
        return $this->modified;
    }

    public function withMessage(
        string $message
    ) : HandlerInterface {
        $clone = clone $this;
        $clone->message = $message;
        return $clone;
    }

    public function withType(
        Type $type
    ) : HandlerInterface {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function withLastModified(
        DateTimeImmutable $modified
    ): HandlerInterface {
        $clone = clone $this;
        $clone->modified = $modified;
        return $clone;
    }
}
