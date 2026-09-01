<?php

declare(strict_types=1);

use ilCronJob;
use ilCronJobResult;
use ILIAS\Cron\Schedule\CronJobScheduleType;
use ILIAS\DI\Exceptions\Exception;
use Leifos\VedaConnector\I\FactoryInterface as VedaFactoryInterface;
use Leifos\VedaConnector\Factory as VedaFactory;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\ErrorCode;
use Leifos\VedaConnector\I\PDFSendStatus\DB\Element\SendStatus;
use Leifos\VedaConnector\I\PluginInterface;
use Leifos\VedaConnector\I\Settings\Name;

/**
 * @ilCtrl_isCalledBy ilVedaConnectorCertificateCronJob: ilObjComponentSettingsGUI
 */
class ilVedaConnectorCertificateCronJob extends ilCronJob
{
    public const JOB_ID = 'vedaimp_cert';

    protected VedaFactoryInterface $veda_factory;
    protected ilCronManager $cron_manager;

    public function __construct()
    {
        global $DIC;
        $this->veda_factory = VedaFactory::getInstance();
        $this->cron_manager = $DIC->cron()->manager();
    }

    public function getId(): string
    {
        return self::JOB_ID;
    }

    public function getTitle(): string
    {
        return PluginInterface::PNAME;
    }

    public function getDescription(): string
    {
        return $this->veda_factory->plugin()->txt('cron_job_cert_info');
    }

    public function hasAutoActivation(): bool
    {
        return false;
    }

    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    public function getDefaultScheduleType(): CronJobScheduleType
    {
        return CronJobScheduleType::SCHEDULE_TYPE_IN_MINUTES;;
    }

    public function getDefaultScheduleValue(): ?int
    {
        return $this->veda_factory->settings()->handler()->readAsInt(Name::CRON_INTERVAL);
    }

    public function run(): ilCronJobResult
    {
        $logger = $this->veda_factory->logger()->handler();
        $elearning_plattform_api = $this->veda_factory->api()->eLearningPlattform()->handler();
        $certificate_handler = $this->veda_factory->pdfSendStatus()->certificate()->handler();
        $pdf_send_status_db = $this->veda_factory->pdfSendStatus()->db()->handler();
        $pdf_send_db_key_factory = $this->veda_factory->pdfSendStatus()->db()->key();
        $not_send_key = $pdf_send_db_key_factory->handler()->withSendStatuses(SendStatus::NOT_SEND);
        $not_send_statuses = $pdf_send_status_db->getByKey($not_send_key);
        $ping_counter = 0;
        $send_counter = 0;
        $logger->debug(sprintf('Start sending certificate data of %s certificates', $not_send_statuses->count()));
        foreach ($not_send_statuses as $pdf_send_status) {
            if ($ping_counter++ > 30) {
                $ping_counter = 0;
                $this->cron_manager->ping(self::JOB_ID);
            }
            $certificate_id = -1;
            try {
                $certificate_id = $certificate_handler->getCertificateId(
                    $pdf_send_status->getParticipantId(),
                    $pdf_send_status->getCourseId()
                );
            } catch (Exception $e) {
                $logger->debug(sprintf('FAILED: Handling of certificate issued event, certificate id not found with error message: %s', $e->getMessage()));
                $pdf_send_status = $pdf_send_status
                    ->withErrorCode(ErrorCode::CERTIFICATE_ID_NOT_FOUND)
                    ->withSendStatus(SendStatus::NOT_SEND);
                continue;
            }
            try {
                $certificate_file_name = $certificate_handler->createCertificateFileName($certificate_id);
                $certificate_content = $certificate_handler->createCertificateContent($certificate_id);
            } catch (Exception $e) {
                $logger->debug('FAILED: Handling of certificate issued event, certificate content or name could not be created.');
                $pdf_send_status = $pdf_send_status
                    ->withErrorCode(ErrorCode::CONTENT_COULD_NOT_BE_CREATED)
                    ->withSendStatus(SendStatus::NOT_SEND);
                $pdf_send_status_db->updateByElement($pdf_send_status);
                $result = new ilCronJobResult();
                $result->setStatus(ilCronJobResult::STATUS_CRASHED);
                $result->setMessage($e->getMessage());
                $this->veda_factory->logger()->handler()->warning(sprintf('Certificate send failed with message: %s', $e->getMessage()));
                return $result;
            }
            $success = $elearning_plattform_api->sendCertificate(
                $pdf_send_status->getCourseOId(),
                $pdf_send_status->getParticipantOId(),
                $certificate_file_name,
                $certificate_content
            );
            if ($success) {
                $logger->debug('SUCCESS: Handling of certificate issued event.');
                $pdf_send_status = $pdf_send_status
                    ->withSendStatus(SendStatus::SEND)
                    ->withSendDate(new \DateTimeImmutable());
                $pdf_send_status_db->updateByElement($pdf_send_status);
                $send_counter++;
            }
            if (!$success) {
                $logger->debug('FAILED: Handling of certificate issued event, certificate could not be send.');
                $pdf_send_status = $pdf_send_status
                    ->withErrorCode(ErrorCode::COULD_NOT_BE_SEND)
                    ->withSendStatus(SendStatus::NOT_SEND);
                $pdf_send_status_db->updateByElement($pdf_send_status);
                $result = new ilCronJobResult();
                $result->setStatus(ilCronJobResult::STATUS_CRASHED);
                $result->setMessage($this->veda_factory->plugin()->txt('cron_job_cert_info_connection_error'));
                $this->veda_factory->logger()->handler()->warning('Certificate send failed.');
                return $result;
            }
        }
        $logger->debug(sprintf('SUMMARY: Send/Generated %s certificates', $send_counter));
        $result = new ilCronJobResult();
        $result->setStatus(ilCronJobResult::STATUS_OK);
        return $result;
    }
}
