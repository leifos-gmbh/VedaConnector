<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Organisation;

use Leifos\VedaConnector\GeneratedOpenApi\Model\Organisation;

interface HandlerInterface
{
    public function getOrganisation(string $orgr_oid) : ?Organisation;
}
