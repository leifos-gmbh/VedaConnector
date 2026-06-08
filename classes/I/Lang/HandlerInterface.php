<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Lang;

interface HandlerInterface
{
    public function pluginTxt(
        string $key
    ): string;

    public function iliasTxt(
        string $key
    ): string;
}
