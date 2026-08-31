<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\PDFSendStatus\Table;

use ILIAS\UI\Component\Table\DataRetrieval;

interface DataRetrievalInterface extends DataRetrieval
{
    public const LNG_ICON_SEND_STATUS_CHECKED = "icon_send_status_checked";
    public const LNG_ICON_SEND_STATUS_UNCHECKED = "icon_send_status_unchecked";

    public const LNG_ICON_PASSED_STATUS_CHECKED = "icon_passed_status_checked";
    public const LNG_ICON_PASSED_STATUS_UNCHECKED = "icon_passed_status_unchecked";
}
