<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Adapter;

use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;

interface UserImportInterface
{
    public const IMPORT_MODE_STANDARD = 0;
    public const IMPORT_MODE_SIFA = 1;

    public function import(
        ParticipantsInterface $participants,
        int $import_mode
    ) : void;
}
