<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB\Element;

use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;

class Handler implements UserDBElementInterface
{
    private string $oid;
    private string $login;
    private Status $status_pwd;
    private Status $status_created;
    private bool $import_failure;

    public function __construct() {
        $this->oid = '';
        $this->login = '';
        $this->status_pwd = Status::NONE;
        $this->status_created = Status::NONE;
        $this->import_failure = false;
    }

    public function getOid() : string
    {
        return $this->oid;
    }

    public function getLogin() : string
    {
        return $this->login;
    }

    public function getPasswordStatus() : Status
    {
        return $this->status_pwd;
    }

    public function getCreationStatus() : Status
    {
        return $this->status_created;
    }

    public function isImportFailure() : bool
    {
        return $this->import_failure;
    }

    public function withOid(
        string $oid
    ): UserDBElementInterface {
        $clone = clone $this;
        $clone->oid = $oid;
        return $clone;
    }

    public function withLogin(
        string $login
    ): UserDBElementInterface {
        $clone = clone $this;
        $clone->login = $login;
        return $clone;
    }

    public function withPasswordStatus(
        Status $status
    ): UserDBElementInterface {
        $clone = clone $this;
        $clone->status_pwd = $status;
        return $clone;
    }

    public function withCreationStatus(
        Status $status
    ): UserDBElementInterface {
        $clone = clone $this;
        $clone->status_created = $status;
        return $clone;
    }

    public function withImportStatusFailed(
        bool $value
    ): UserDBElementInterface {
        $clone = clone $this;
        $clone->import_failure = $value;
        return $clone;
    }
}
