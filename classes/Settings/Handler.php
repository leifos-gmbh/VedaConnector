<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\Settings;

use ilSetting;
use Leifos\VedaConnector\I\Settings\HandlerInterface;
use Leifos\VedaConnector\I\Settings\Name;

class Handler implements HandlerInterface
{
    protected ilSetting $settings;

    public function __construct()
    {
        $this->settings = new ilSetting(HandlerInterface::SETTING_MODULE);
    }

    public function read(
        Name $setting_name
    ): string {
        return $this->settings->get($setting_name->value, '');
    }

    public function readAsBool(
        Name $setting_name
    ): bool {
        return (bool) $this->read($setting_name);
    }

    public function readAsInt(
        Name $setting_name
    ): int {
        return (int) $this->read($setting_name);
    }

    public function write(
        Name $setting_name,
        string $value
    ): void {
        $this->settings->set($setting_name->value, $value);
    }

    public function writeBool(
        Name $setting_name,
        bool $value
    ): void {
        $this->write($setting_name, (string) $value);;
    }

    public function writeInt(
        Name $setting_name,
        int $value
    ): void {
        $this->write($setting_name, (string) $value);
    }

    public function hasSettingsForConnectionTest() : bool
    {
        return
            strcmp($this->read(Name::REST_URL), "") !== 0 &&
            strcmp($this->read(Name::REST_TOKEN), "") !== 0 &&
            strcmp($this->read(Name::PLATTFORM_ID), "") !== 0;
    }

    public function udfFieldsAvailable(): bool
    {
        return strcmp($this->read(Name::UDF_TUTOR_ID), '') == 0
            || strcmp($this->read(Name::UDF_MEMBER_ID), '') == 0
            || strcmp($this->read(Name::UDF_COMPANION_ID), '') == 0
            || strcmp($this->read(Name::UDF_SUPERVISOR_ID), '') == 0
            || strcmp($this->read(Name::UDF_SUPERVISOR), '') == 0
            || strcmp($this->read(Name::UDF_SUPERVISOR_EMAIL), '') == 0;
    }

    public function mdFieldsAvailable(): bool
    {
        return strcmp($this->read(Name::MD_RECORD_AUSBILDUNG), '') == 0
            || strcmp($this->read(Name::MD_RECORD_ABSCHNITT), '') == 0
            || strcmp($this->read(Name::MD_FIELD_AUSBILDUNGSZUGABSCHNITT), '') == 0
            || strcmp($this->read(Name::MD_FIELD_AUSBILDUNGSZUG), '') == 0
            || strcmp($this->read(Name::MD_FIELD_AUSBILDUNGSGANGABSCHNITT), '') == 0
            || strcmp($this->read(Name::MD_FIELD_AUSBILDUNGSGANG), '') == 0;
    }

    public function udfFieldsAsArray(): array
    {
        return [
            Name::UDF_TUTOR_ID->value => $this->readAsInt(Name::UDF_TUTOR_ID),
            Name::UDF_SUPERVISOR_ID->value => $this->readAsInt(Name::UDF_SUPERVISOR_ID),
            Name::UDF_SUPERVISOR->value => $this->readAsInt(Name::UDF_SUPERVISOR),
            Name::UDF_SUPERVISOR_EMAIL->value => $this->readAsInt(Name::UDF_SUPERVISOR_EMAIL),
            Name::UDF_MEMBER_ID->value => $this->readAsInt(Name::UDF_MEMBER_ID),
            Name::UDF_COMPANION_ID->value => $this->readAsInt(Name::UDF_COMPANION_ID),
        ];
    }
}
