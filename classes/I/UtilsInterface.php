<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I;

use DateTime;

interface UtilsInterface
{
    public static function compareOidsEqual(
        string $first = null,
        string $second = null
    ) : bool;

    public static function isValidDate(
        ?DateTime $start,
        ?DateTime $end
    ) : bool;
}
