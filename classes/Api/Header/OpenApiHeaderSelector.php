<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\Api;

use Leifos\VedaConnector\GeneratedOpenApi\Configuration;
use Leifos\VedaConnector\GeneratedOpenApi\HeaderSelector as OpenApiClientHeaderSelector;
use Leifos\VedaConnector\I\Settings\HandlerInterface as SettingsInterface;
use Leifos\VedaConnector\I\Settings\Name as SettingsName;

class OpenApiHeaderSelector extends OpenApiClientHeaderSelector
{
    public function __construct(
        protected SettingsInterface $settings,
        protected Configuration $config
    ) {
    }

    public function selectHeaders(
        array $accept,
        string $contentType,
        bool $isMultipart
    ) : array {
        $headers = parent::selectHeaders($accept, $contentType, $isMultipart);
        $headers[SettingsName::HEADER_TOKEN->value] = $this->config->getAccessToken();
        if ($this->settings->readAsBool(SettingsName::ADD_HEADER_AUTH)) {
            $headers[$this->settings->read(SettingsName::ADD_HEADER_NAME)] = $this->settings->read(SettingsName::ADD_HEADER_VALUE);
        }
        return $headers;
    }
}
