<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UDF;

use ilDBInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\UDF\DB\FactoryInterface as DBFactoryInterface;
use Leifos\VedaConnector\I\UDF\FactoryInterface;
use Leifos\VedaConnector\UDF\DB\Factory as DBFactory;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected SettingsFactoryInterface $settings_factory
    ) {
    }

    public function db(): DBFactoryInterface
    {
        return new DBFactory(
            $this->db,
            $this->settings_factory
        );
    }
}
