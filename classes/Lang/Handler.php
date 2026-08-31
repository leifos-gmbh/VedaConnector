<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Lang;

use ilLanguage;
use Leifos\VedaConnector\I\Lang\HandlerInterface;
use Leifos\VedaConnector\I\PluginInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        protected PluginInterface $plugin,
        protected ilLanguage $lang
    ) {
    }

    public function pluginTxt(
        string $key
    ): string {
        return $this->plugin->txt($key);
    }

    public function iliasTxt(
        string $key
    ): string {
        return $this->lang->txt($key);
    }
}
