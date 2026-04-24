<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\DB;

use Leifos\VedaConnector\I\UserStatus\DB\Element\CollectionInterface as UserDBElementCollectionInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;

interface HandlerInterface
{
    public function update(
        UserDBElementInterface $element
    ) : void;

    public function deleteByOId(
        string $oid
    ) : void;

    public function deleteById(
        int $usr_id
    ) : void;

    public function lookupByOId(
        string $oid
    ) : ?UserDBElementInterface;

    public function lookupById(
        int $ref_id
    ) : ?UserDBElementInterface;

    public function lookupAll() : UserDBElementCollectionInterface;
}
