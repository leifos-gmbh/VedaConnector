<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\UserStatus\Table;

interface ImportResultInterface
{
    public function init() : void;

    public function parse() : void;
}
