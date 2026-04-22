<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Adapter;

use Leifos\VedaConnector\I\Api\ELearningPlattform\Collections\ParticipantsInterface;

interface UserImportInterface
{
    public function import(
        ParticipantsInterface $participants,
        int $import_mode
    ) : void;
}
