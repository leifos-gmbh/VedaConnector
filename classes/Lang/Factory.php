<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Lang;

use ilLanguage;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\I\Lang\FactoryInterface;
use Leifos\VedaConnector\I\Lang\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilVedaConnectorPlugin $plugin,
        protected ilLanguage $lang
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->plugin,
            $this->lang
        );
    }
}
