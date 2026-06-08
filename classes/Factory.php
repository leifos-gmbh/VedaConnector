<?php

declare(strict_types=1);

namespace Leifos\VedaConnector;

use ilComponentFactory;
use ilDBInterface;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\DI\UIServices as UIServices;
use ILIAS\HTTP\Services as HTTPServices;
use ilLanguage;
use ilLogger;
use ilMailMimeSenderFactory;
use ilObjectDefinition;
use ilObjUser;
use ilRbacAdmin;
use ilRbacReview;
use ilTree;
use ilVedaConnectorPlugin;
use Leifos\VedaConnector\Api\Factory as ApiFactory;
use Leifos\VedaConnector\Builder\Factory as BuilderFactory;
use Leifos\VedaConnector\CourseStatus\Factory as CourseFactory;
use Leifos\VedaConnector\Exception\Factory as ExceptionFactory;
use Leifos\VedaConnector\I\Api\FactoryInterface as ApiFactoryInterface;
use Leifos\VedaConnector\I\Builder\FactoryInterface as BuilderFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\FactoryInterface as CourseFactoryInterface;
use Leifos\VedaConnector\I\Exception\FactoryInterface as ExceptionFactoryInterface;
use Leifos\VedaConnector\I\FactoryInterface;
use Leifos\VedaConnector\I\IdValidatorInterface;
use Leifos\VedaConnector\I\ImporterInterface;
use Leifos\VedaConnector\I\InputFields\FactoryInterface as InputFieldsFactoryInterface;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;
use Leifos\VedaConnector\I\Mail\FactoryInterface as MailFactoryInterface;
use Leifos\VedaConnector\I\MDClaiming\FactoryInterface as MDClaimingFactoryInterface;
use Leifos\VedaConnector\I\PDFSendStatus\FactoryInterface as PDFSendStatusFactoryInterface;
use Leifos\VedaConnector\I\PluginInterface;
use Leifos\VedaConnector\I\Settings\FactoryInterface as SettingsFactoryInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\FactoryInterface as TrainingProgramModulesFactoryInterface;
use Leifos\VedaConnector\I\UDF\FactoryInterface as UDFFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\FactoryInterface as UserFactoryInterface;
use Leifos\VedaConnector\I\Utils\FactoryInterface as UtilsFactoryInterface;
use Leifos\VedaConnector\InputFields\Factory as InputFieldsFactory;
use Leifos\VedaConnector\Lang\Factory as LangFactory;
use Leifos\VedaConnector\Logger\Factory as LoggerFactory;
use Leifos\VedaConnector\Mail\Factory as MailFactory;
use Leifos\VedaConnector\MDClaiming\Factory as MDClaimingFactory;
use Leifos\VedaConnector\PDFSendStatus\Factory as PDFSendStatusFactory;
use Leifos\VedaConnector\Settings\Factory as SettingsFactory;
use Leifos\VedaConnector\TrainingProgramModules\Factory as TrainingProgramModulesFactory;
use Leifos\VedaConnector\UDF\Factory as UDFFactory;
use Leifos\VedaConnector\UserStatus\Factory as UserFactory;
use Leifos\VedaConnector\Utils\Factory as UtilsFactory;

class Factory implements FactoryInterface
{
    /**
     * @var IdValidator[]
     */
    protected static array $validator_instances;
    protected static ImporterInterface $importer;
    protected static FactoryInterface $instance;
    protected ilTree $repository_tree;
    protected ilLogger $logger;
    protected ilComponentFactory $component_factory;
    protected ilMailMimeSenderFactory $sender_factory;
    protected ilDBInterface $db;
    protected ilObjUser $user;
    protected ilObjectDefinition $object_definition;
    protected ilRbacAdmin $rbac_admin;
    protected ilRbacReview $rbac_review;
    protected ilLanguage $lang;
    protected HTTPServices $http_services;
    protected UIServices $ui_services;
    protected DataFactory $data_factory;

    public function __construct()
    {
        global $DIC;
        $this->repository_tree = $DIC->repositoryTree();
        $this->logger = $DIC->logger()->vedaimp();
        $this->component_factory = $DIC['component.factory'];
        $this->sender_factory = $DIC->mail()->mime()->senderFactory();
        $this->db = $DIC->database();
        $this->user = $DIC->user();
        $this->object_definition = $DIC['objDefinition'];
        $this->rbac_admin = $DIC->rbac()->admin();
        $this->rbac_review = $DIC->rbac()->review();
        $this->lang = $DIC->language();
        $this->http_services = $DIC->http();
        $this->ui_services = $DIC->ui();
        $this->data_factory = new DataFactory();
    }

    public static function getInstance(): FactoryInterface
    {
        if (!isset(Factory::$instance)) {
            Factory::$instance = new Factory();
        }
        return Factory::$instance;
    }

    public function plugin(): PluginInterface
    {
        return ilVedaConnectorPlugin::getInstance();
    }

    public function importer(): ImporterInterface
    {
        if (!isset(Factory::$importer)) {
            Factory::$importer = new Importer(
                $this->logger()->handler(),
                $this->settings()->handler(),
                $this->api()->handler(),
                $this->exception()
            );
        }
        return Factory::$importer;
    }

    public function utils(): UtilsFactoryInterface
    {
        return new UtilsFactory(
            $this->logger()
        );
    }

    public function logger(): LoggerFactoryInterface
    {
        return new LoggerFactory(
            $this->logger,
            $this->settings()
        );
    }

    public function userStatus(): UserFactoryInterface
    {
        return new UserFactory(
            $this->plugin()->getDirectory(),
            $this->db,
            $this->logger(),
            $this->lang()
        );
    }

    public function coursStatus(): CourseFactoryInterface
    {
        return new CourseFactory(
            $this->plugin()->getDirectory(),
            $this->db,
            $this->logger(),
            $this->repository_tree,
            $this->lang()
        );
    }

    public function settings(): SettingsFactoryInterface
    {
        return new SettingsFactory();
    }

    public function validator(
        int $reference_id
    ): IdValidatorInterface {
        if (!isset(Factory::$validator_instances)) {
            Factory::$validator_instances = [];
        }
        if (!isset(Factory::$validator_instances[$reference_id])) {
            Factory::$validator_instances[$reference_id] = new IdValidator(
                $reference_id,
                $this->repository_tree,
                $this->plugin()->getTemplate('tpl.validation_error.html'),
                $this->lang()->handler(),
                $this->mdClaiming()->db()->handler(),
                $this->logger()->handler(),
                $this->api()->handler()
            );
        }
        return Factory::$validator_instances[$reference_id];
    }

    public function mail(): MailFactoryInterface
    {
        return new MailFactory(
            $this->sender_factory,
            $this->logger(),
            $this->db,
            $this->settings()
        );
    }

    public function inputFields(): InputFieldsFactoryInterface
    {
        return new InputFieldsFactory(
            $this->lang()
        );
    }

    public function trainingProgrammModules(): TrainingProgramModulesFactoryInterface
    {
        return new TrainingProgramModulesFactory(
            $this->db,
            $this->logger()
        );
    }

    public function api(): ApiFactoryInterface
    {
        return new ApiFactory(
            $this->user,
            $this->object_definition,
            $this->rbac_admin,
            $this->rbac_review,
            $this->logger(),
            $this->mail()->db(),
            $this->settings(),
            $this->builder(),
            $this->userStatus()->db(),
            $this->coursStatus()->db(),
            $this->mdClaiming()->db(),
            $this->udf()->db(),
            $this->utils(),
            $this->exception()
        );
    }

    public function builder(): BuilderFactoryInterface
    {
        return new BuilderFactory(
            $this->userStatus()->db(),
            $this->coursStatus()->db(),
            $this->mail()->db(),
            $this->trainingProgrammModules()->db()
        );
    }

    public function mdClaiming(): MDClaimingFactoryInterface
    {
        return new MDClaimingFactory(
            $this->logger(),
            $this->settings(),
            $this->db,
            $this->repository_tree,
        );
    }

    public function udf(): UDFFactoryInterface
    {
        return new UDFFactory(
            $this->db,
            $this->settings()
        );
    }

    public function exception(): ExceptionFactoryInterface
    {
        return new ExceptionFactory(
            $this->lang()->handler()
        );
    }

    public function pdfSendStatus(): PDFSendStatusFactoryInterface
    {
        return new PDFSendStatusFactory(
            $this->db,
            $this->user,
            $this->data_factory,
            $this->ui_services,
            $this->lang(),
            $this->http_services,
            $this->logger()
        );
    }

    public function lang(): LangFactoryInterface
    {
        return new LangFactory(
            $this->plugin(),
            $this->lang
        );
    }
}
