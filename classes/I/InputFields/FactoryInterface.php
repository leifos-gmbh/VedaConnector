<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\InputFields;

interface FactoryInterface
{
    public function refIdNumber(
        string $title = "",
        string $post_var = ""
    ): RefIdNumberInterface;
}
