<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\Table;

use ilCalendarSettings;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\DI\UIServices as UIServices;
use ILIAS\HTTP\Services as HTTPServices;
use ILIAS\UI\Component\Table\Data as DataTable;
use ilObjUser;
use Leifos\VedaConnector\I\Lang\HandlerInterface as LangInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\DataRetrievalInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\HandlerInterface;

class Handler implements HandlerInterface
{
    protected DataTable $table;

    public function __construct(
        protected DataRetrievalInterface $data_retrieval,
        protected ilObjUser $user,
        protected DataFactory $data_factory,
        protected UIServices $ui_services,
        protected LangInterface $lang,
        protected HTTPServices $http_services
    ) {
    }

    protected function getColumns(): array
    {
        if ((int) $this->user->getTimeFormat() === ilCalendarSettings::TIME_FORMAT_12) {
            $format = $this->data_factory->dateFormat()->withTime12($this->user->getDateFormat());
        } else {
            $format = $this->data_factory->dateFormat()->withTime24($this->user->getDateFormat());
        }
        $columns = [
            HandlerInterface::TABLE_COL_COURSE_OID => $this->ui_services->factory()->table()->column()->text(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_COURSE_OID)
            )
                ->withHighlight(true),
            HandlerInterface::TABLE_COL_PARTICIPANT_OID => $this->ui_services->factory()->table()->column()->text(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_PARTICIPANT_OID)
            )
                ->withHighlight(true),
            HandlerInterface::TABLE_COL_PASSED_STATUS => $this->ui_services->factory()->table()->column()->statusIcon(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_PASSED_STATUS)
            )
                ->withHighlight(true),
            HandlerInterface::TABLE_COL_PASSED_DATE => $this->ui_services->factory()->table()->column()->date(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_PASSED_DATE),
                $format
            ),
            HandlerInterface::TABLE_COL_SEND_STATUS => $this->ui_services->factory()->table()->column()->statusIcon(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_SEND_STATUS)
            )
                ->withHighlight(true),
            HandlerInterface::TABLE_COL_SEND_DATE => $this->ui_services->factory()->table()->column()->date(
                $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_COL_SEND_DATE),
                $format
            )
                ->withHighlight(true),
            self::TABLE_COL_ERROR_CODE => $this->ui_services->factory()->table()->column()->number(
                $this->lang->pluginTxt(self::LNG_TABLE_COL_ERROR_CODE)
            )
                ->withHighlight(true)
        ];
        return $columns;
    }

    protected function getActions(): array
    {
        return [];
    }

    protected function initTable(): void
    {
        if (isset($this->table)) {
            return;
        }
        $this->table = $this->ui_services->factory()->table()->data(
            $this->lang->pluginTxt(HandlerInterface::LNG_TABLE_NAME),
            $this->getColumns(),
            $this->data_retrieval
        )
            ->withId(HandlerInterface::TABLE_ID)
            ->withActions($this->getActions())
            ->withRequest($this->http_services->request());
    }

    public function handleCommands(): void
    {
        $this->initTable();
    }

    public function getHTML(): string
    {
        $this->initTable();
        return $this->ui_services->renderer()->render($this->table);
    }
}
