<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB;

use Leifos\VedaConnector\I\Mail\DB\Element\FactoryInterface as EntryFactoryInterface;

interface FactoryInterface
{
    public function handler(): HandlerInterface;

    public function element(): EntryFactoryInterface;

    public function elementBuilder(): BuilderInterface;
}
