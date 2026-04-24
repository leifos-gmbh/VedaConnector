<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB\Element;

interface HandlerInterface
{
    public function getOid() : string;

    public function getLogin() : string;

    public function getPasswordStatus() : Status;

    public function getCreationStatus() : Status;

    public function isImportFailure() : bool;

    public function withOid(
        string $oid
    ) : HandlerInterface;

    public function withLogin(
        string $login
    ) : HandlerInterface;

    public function withPasswordStatus(
        Status $status
    ) : HandlerInterface;

    public function withCreationStatus(
        Status $status
    ) : HandlerInterface;

    public function withImportStatusFailed(
        bool $value
    ) : HandlerInterface;
}
