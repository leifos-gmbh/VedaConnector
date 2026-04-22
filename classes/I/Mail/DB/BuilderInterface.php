<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Mail\DB;

use Leifos\VedaConnector\I\Mail\DB\Element\Type;

interface BuilderInterface
{
    public function withType(
        Type $type
    ) : BuilderInterface;

    public function withMessage(
        string $message
    ) : BuilderInterface;

    public function store() : void;
}
