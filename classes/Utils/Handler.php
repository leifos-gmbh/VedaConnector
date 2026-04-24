<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\Utils;

use DateTime;
use ilDate;
use ilDateTime;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\Utils\HandlerInterface;

class Handler implements HandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function compareOidsEqual(
        string $first = null,
        string $second = null
    ) : bool {
        return strcmp(strtolower($first), strtolower($second)) === 0;
    }

    public function isValidDate(
        ?DateTime $start = null,
        ?DateTime $end = null
    ) : bool {
        $ilstart = is_null($start) ? null : new ilDate($start->format('Y-m-d'), IL_CAL_DATE);
        $ilend = is_null($end) ? null : new ilDate($end->format('Y-m-d'), IL_CAL_DATE);
        $now = new ilDate(time(), IL_CAL_UNIX);
        $is_valid = is_null($start) && is_null($end);
        if (is_null($ilstart) && !is_null($ilend)) {
            $is_valid = ilDateTime::_after($ilend, $now, IL_CAL_DAY) || ilDateTime::_equals($ilend, $now, IL_CAL_DAY);
            $this->logger->debug('Ending date is ' . ($is_valid ? 'valid' : 'invalid'));
        }
        if (!is_null($ilstart) && is_null($ilend)) {
            $is_valid = ilDateTime::_before($ilstart, $now, IL_CAL_DAY) || ilDateTime::_equals($ilstart, $now, IL_CAL_DAY);
            $this->logger->debug('Starting date is ' . ($is_valid ? 'valid' : 'invalid'));
        }
        if (!is_null($ilstart) && !is_null($ilend)) {
            $is_valid = ilDateTime::_within($now, $ilstart, $ilend, IL_CAL_DAY) || ilDateTime::_equals($now, $ilend, ilDateTime::DAY);
            $this->logger->debug('Date is ' . ($is_valid ? 'valid' : 'invalid'));
        }
        return $is_valid;
    }
}
