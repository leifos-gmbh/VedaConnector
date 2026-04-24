<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UDF\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\UDF\DB\FactoryInterface;
use Leifos\VedaConnector\I\UDF\DB\HandlerInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected SettingsFactoryInterface $settings_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->db,
            $this->settings_factory->handler()
        );
    }
}
