<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\CourseStatus\DB;

use ilDBInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\BuilderInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\Element\FactoryInterface as CourseDBElementFactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\FactoryInterface;
use Leifos\VedaConnector\I\CourseStatus\DB\HandlerInterface;
use Leifos\VedaConnector\CourseStatus\DB\Element\Factory as CourseDBElementFactory;
use Leifos\VedaConnector\I\Logger\FactoryInterface as LoggerFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected ilDBInterface $db,
        protected LoggerFactoryInterface $logger_factory
    ) {
    }

    public function handler(): HandlerInterface
    {
        return new Handler(
            $this->element(),
            $this->db,
            $this->logger_factory->handler()
        );
    }

    public function element(): CourseDBElementFactoryInterface
    {
        return new CourseDBElementFactory();
    }

    public function elementBuilder(): BuilderInterface
    {
        return new Builder(
            $this->element(),
            $this->handler(),
            $this->logger_factory->handler()
        );
    }
}
