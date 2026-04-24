<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Utils;

use DateTime;

interface HandlerInterface
{
    public function compareOidsEqual(
        string $first = null,
        string $second = null
    ) : bool;

    public function isValidDate(
        ?DateTime $start,
        ?DateTime $end
    ) : bool;
}
