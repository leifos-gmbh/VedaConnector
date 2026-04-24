<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UDF\DB;

interface HandlerInterface
{
    /**
     * @return int[]
     */
    public function getUserIdsForFieldAndOId(
        ?string $oid,
        int $field_id
    ): array;
}
