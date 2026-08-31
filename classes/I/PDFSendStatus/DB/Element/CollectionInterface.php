<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\DB\Element;

use Countable;
use Iterator;

interface CollectionInterface extends Iterator, Countable
{
    public function key(): int;

    public function current(): HandlerInterface;
}
