<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I;

interface ImporterInterface
{
    /** @var int */
    public const IMPORT_TYPE_UNDEFINED = 0;

    /** @var int */
    public const IMPORT_TYPE_SIFA = 1;

    /** @var int */
    public const IMPORT_TYPE_STANDARD = 2;

    /** @var string */
    public const IMPORT_USR_ALL = 'usr_all';

    /** @var string */
    public const IMPORT_USR_INCREMENTAL = 'usr_incremental';

    /** @var string */
    public const IMPORT_CRS = 'crs';

    /** @var string */
    public const IMPORT_MEM = 'mem';

    /** @var int */
    public const IMPORT_NONE = 0;

    /** @var int */
    public const IMPORT_ALL = 1;

    /** @var int */
    public const IMPORT_SELECTED = 2;

    public function import(
        int $import_type,
        bool $all,
        array $types = []
    ): void;
}
