<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB\Element;

interface HandlerInterface
{
    public function getOID() : string;

    public function getType() : Type;

    public function withOId(
        string $oid
    ): HandlerInterface;

    public function withType(
        Type $type
    ): HandlerInterface;

    public function isPracticalTraining() : bool;

    public function isSelfLearning() : bool;
}
