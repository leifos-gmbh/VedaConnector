<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB;

use Leifos\VedaConnector\I\TrainingProgramModules\DB\Element\HandlerInterface as TrainingProgramModulesDBElementInterface;

interface HandlerInterface
{
    public const TABLE_NAME = 'cron_crnhk_vedaimp_seg';

    public function update(
        TrainingProgramModulesDBElementInterface $element
    ): void;

    public function deleteByOId(
        string $oid
    ): void;

    public function lookupByOId(
        string $oid
    ) : ?TrainingProgramModulesDBElementInterface;
}
