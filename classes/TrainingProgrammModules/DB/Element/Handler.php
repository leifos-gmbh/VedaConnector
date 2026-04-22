<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

namespace Leifos\VedaConnector\TrainingProgramModules\DB\Element;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface;
use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\Type;

class Handler implements HandlerInterface
{
    protected string $oid;
    protected Type $type;

    public function __construct() {
        $this->oid = "";
        $this->type = Type::NULL;
    }

    public function withOId(
        string $oid
    ): HandlerInterface {
        $clone = clone $this;
        $clone->oid = $oid;
        return $clone;
    }

    public function withType(
        Type $type
    ): HandlerInterface {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function getOID() : string
    {
        return $this->oid;
    }

    public function getType() : Type
    {
        return $this->type;
    }

    public function isPracticalTraining() : bool
    {
        return $this->type == Type::PRAKTIKUM;
    }

    public function isSelfLearning() : bool
    {
        return $this->type == Type::SELF_LEARNING;
    }
}
