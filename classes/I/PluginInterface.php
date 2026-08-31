<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I;

use ilTemplate;

interface PluginInterface
{
    public const PNAME = 'VedaConnector';

    public const PLUGIN_ID = 'vedaimp';

    public function getDirectory(): string;

    public function getTemplate(string $a_template, bool $a_par1 = true, bool $a_par2 = true): ilTemplate;

    public function txt(
        string $a_var
    ): string;
}
