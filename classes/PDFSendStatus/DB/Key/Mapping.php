<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\DB\Key;

use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\MappingInterface;

class Mapping implements MappingInterface
{
    protected array $mappings;

    public function withMapping(
        string $key,
        string $value
    ): MappingInterface {
        $clone = clone $this;
        $clone->mappings[$key] = $value;
        return $clone;
    }

    public function get(
        string $key
    ): string {
        return $this->mappings[$key] ?? '';
    }

    public function has(
        string $key
    ): bool {
        return isset($this->mappings[$key]);
    }
}
