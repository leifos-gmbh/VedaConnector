<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface as TrainingProgramModulesDBElementInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\Type;

interface BuilderInterface
{
    public function withType(
        Type $type
    ): BuilderInterface;

    public function withOId(
        string $oid,
        bool $load_from_db = true
    ): BuilderInterface;

    public function get(): TrainingProgramModulesDBElementInterface;

    public function store() : void;
}
