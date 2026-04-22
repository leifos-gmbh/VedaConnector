<?php

namespace Leifos\VedaConnector\I\Settings;

interface HandlerInterface
{
    public const SETTING_MODULE = 'vedaimp';

    public function hasSettingsForConnectionTest(): bool;

    public function read(
        Name $setting_name
    ): string;

    public function readAsBool(
        Name $setting_name
    ): bool;

    public function readAsInt(
        Name $setting_name
    ): int;

    public function write(
        Name $setting_name,
        string $value
    ): void;


    public function writeBool(
        Name $setting_name,
        bool $value
    ): void;

    public function writeInt(
        Name $setting_name,
        int $value
    ): void;
}
