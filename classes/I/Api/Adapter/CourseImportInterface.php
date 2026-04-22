<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Adapter;

interface CourseImportInterface
{
    public function import(): void;

    public function handleAfterCloningDependenciesEvent(int $source_id, int $target_id, int $copy_id) : void;

    public function handleAfterCloningEvent(int $a_source_id, int $a_target_id, int $a_copy_id) : void;
}
