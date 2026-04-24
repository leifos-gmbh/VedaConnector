<?php

namespace Leifos\VedaConnector\I\Settings;

interface FactoryInterface
{
    public function handler(): HandlerInterface;
}
