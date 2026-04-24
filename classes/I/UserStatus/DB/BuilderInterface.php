<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB;

use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;

interface BuilderInterface
{
    public function withOID(
        string $oid,
        bool $load_from_db = true
    ) : BuilderInterface;

    public function withLogin(
        string $login
    ) : BuilderInterface;

    public function withPasswordStatus(
        Status $status
    ): BuilderInterface;

    public function withCreationStatus(
        Status $status
    ) : BuilderInterface;

    public function withImportFailure(
        bool $value
    ) : BuilderInterface;

    public function get() : UserDBElementInterface;

    public function store() : void;
}
