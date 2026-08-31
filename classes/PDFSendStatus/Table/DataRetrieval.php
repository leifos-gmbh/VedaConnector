<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\PDFSendStatus\Table;

use Generator;
use ILIAS\Data\Order;
use ILIAS\Data\Range;
use ILIAS\DI\UIServices as UIServices;
use ILIAS\UI\Component\Symbol\Icon\Icon as IconInterface;
use ILIAS\UI\Component\Table\DataRowBuilder;
use ILIAS\UI\Implementation\Component\Symbol\Icon\Icon;
use ilUtil;
use Leifos\VedaConnector\I\Lang\HandlerInterface as LangInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\PassedStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;
use Leifos\VedaConnector\I\PDFSendStatus\DB\HandlerInterface as PDFSendStatusDBInterface;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Key\FactoryInterface as PDFSendStatusDBKeyFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\DataRetrievalInterface;
use Leifos\VedaConnector\I\PDFSendStatus\Table\HandlerInterface as PDFSendStatusTableInterface;

class DataRetrieval implements DataRetrievalInterface
{
    public function __construct(
        protected PDFSendStatusDBInterface $pdf_send_status_db,
        protected PDFSendStatusDBKeyFactoryInterface $pdf_send_status_db_key_factory,
        protected UIServices $ui_services,
        protected LangInterface $lang
    ) {
    }

    public function getRows(
        DataRowBuilder $row_builder,
        array $visible_column_ids,
        Range $range,
        Order $order,
        ?array $filter_data,
        ?array $additional_parameters
    ): Generator {
        $key = $this->pdf_send_status_db_key_factory->handler()
            ->withRange($range)
            ->withOrder(
                $order,
                $this->pdf_send_status_db_key_factory->mapping()
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_COURSE_OID, PDFSendStatusDBInterface::FIELD_NAME_COURSE_OID)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_PARTICIPANT_OID, PDFSendStatusDBInterface::FIELD_NAME_PARTICIPANT_OID)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_SEND_STATUS, PDFSendStatusDBInterface::FIELD_NAME_STATUS_SEND)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_SEND_DATE, PDFSendStatusDBInterface::FIELD_NAME_TIMESTAMP_SEND)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_PASSED_STATUS, PDFSendStatusDBInterface::FIELD_NAME_STATUS_PASSED)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_PASSED_DATE, PDFSendStatusDBInterface::FIELD_NAME_TIMESTAMP_PASSED)
                    ->withMapping(PDFSendStatusTableInterface::TABLE_COL_ERROR_CODE, PDFSendStatusDBInterface::FIELD_NAME_ERROR_CODE)
            );
        $elements = $this->pdf_send_status_db->getByKey($key);
        foreach ($elements as $element) {
            yield $row_builder->buildDataRow(
                (string) $element->getDBSequenceId(),
                [
                    PDFSendStatusTableInterface::TABLE_COL_COURSE_OID => $element->getCourseOid(),
                    PDFSendStatusTableInterface::TABLE_COL_PARTICIPANT_OID => $element->getParticipantOId(),
                    PDFSendStatusTableInterface::TABLE_COL_SEND_STATUS => $this->getSendStatusIcon($element->getSendStatus()),
                    PDFSendStatusTableInterface::TABLE_COL_SEND_DATE => $element->getSendDate(),
                    PDFSendStatusTableInterface::TABLE_COL_PASSED_STATUS => $this->getPassedStatusIcon($element->getPassedStatus()),
                    PDFSendStatusTableInterface::TABLE_COL_PASSED_DATE => $element->getPassedDate(),
                    PDFSendStatusTableInterface::TABLE_COL_ERROR_CODE => $element->getErrorCode()->value,
                ]
            );
        }
    }

    public function getTotalRowCount(
        ?array $filter_data,
        ?array $additional_parameters
    ): ?int {
        return $this->pdf_send_status_db->getByKey($this->pdf_send_status_db_key_factory->createKeyForAllElements())->count();
    }

    protected function getSendStatusIcon(
        SendStatus $send_status
    ): Icon {
        return match ($send_status) {
            SendStatus::NULL => null,
            SendStatus::SEND => $this->ui_services->factory()->symbol()->icon()->custom(
                ilUtil::getImagePath('standard/icon_checked.svg'),
                $this->lang->pluginTxt(DataRetrievalInterface::LNG_ICON_SEND_STATUS_CHECKED),
                IconInterface::MEDIUM
            ),
            SendStatus::NOT_SEND => $this->ui_services->factory()->symbol()->icon()->custom(
                ilUtil::getImagePath('standard/icon_unchecked.svg'),
                $this->lang->pluginTxt(DataRetrievalInterface::LNG_ICON_SEND_STATUS_UNCHECKED),
                IconInterface::MEDIUM
            )
        };
    }

    protected function getPassedStatusIcon(
        PassedStatus $passed_status
    ): Icon {
        return match ($passed_status) {
            PassedStatus::NULL => null,
            PassedStatus::PASSED => $this->ui_services->factory()->symbol()->icon()->custom(
                ilUtil::getImagePath('standard/icon_checked.svg'),
                $this->lang->pluginTxt(DataRetrievalInterface::LNG_ICON_PASSED_STATUS_CHECKED),
                IconInterface::MEDIUM
            ),
            PassedStatus::NOT_PASSED => $this->ui_services->factory()->symbol()->icon()->custom(
                ilUtil::getImagePath('standard/icon_unchecked.svg'),
                $this->lang->pluginTxt(DataRetrievalInterface::LNG_ICON_PASSED_STATUS_UNCHECKED),
                IconInterface::MEDIUM
            )
        };
    }
}
