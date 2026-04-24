<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB\Element;

use DateTimeImmutable;

interface HandlerInterface
{
    public function getId() : int;

    public function getMessage() : string;

    public function withMessage(
        string $message
    ) : HandlerInterface;

    public function getType() : Type;

    public function withType(
        Type $type
    ) : HandlerInterface;

    public function getLastModified() : DateTimeImmutable;

    public function withLastModified(
        DateTimeImmutable $modified
    ) : HandlerInterface;
}
