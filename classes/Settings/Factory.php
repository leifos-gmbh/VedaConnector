<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Settings;

use Leifos\VedaConnector\I\Settings\FactoryInterface;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;

class Factory implements FactoryInterface
{
    public function handler(): SettingsInterface
    {
        return new Handler();
    }
}
