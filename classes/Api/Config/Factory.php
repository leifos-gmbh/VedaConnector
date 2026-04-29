<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Api\Config;

use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\I\Api\Config\FactoryInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactory;
use Leifos\VedaConnector\I\Settings\Name as SettingNames;

class Factory implements FactoryInterface
{
    protected static Configuration $open_api_config;

    public function __construct(
        protected SettingsFactory $settings_factory
    ) {
    }

    public function openApi(): Configuration
    {
        if (!isset(self::$open_api_config)) {
            self::$open_api_config = new Configuration();
            self::$open_api_config->setApiKey(
                SettingNames::HEADER_TOKEN->value,
                $this->settings_factory->handler()->read(SettingNames::REST_TOKEN)
            );
            self::$open_api_config->setHost($this->settings_factory->handler()->read(SettingNames::REST_URL));
            self::$open_api_config->setAccessToken($this->settings_factory->handler()->read(SettingNames::REST_TOKEN));
        }
        return self::$open_api_config;
    }
}
