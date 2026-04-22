<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Adapter;

interface MemberImportInterface
{
    public function import() : void;

    public function handleTrackingEvent(
        int $obj_id,
        int $usr_id,
        int $status
    ) : void;
}
