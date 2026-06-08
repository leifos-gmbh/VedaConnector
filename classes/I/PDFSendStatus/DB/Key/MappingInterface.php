<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Key;

interface MappingInterface
{
    public function withMapping(
        string $key,
        string $value
    ): MappingInterface;

    public function get(
        string $key
    ): string;

    public function has(
        string $key
    ): bool;
}
