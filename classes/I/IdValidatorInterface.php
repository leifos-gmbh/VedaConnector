<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I;

interface IdValidatorInterface
{
    public function validate() : bool;

    public function getErrorMessage() : string;

    public function getSuccessMessage() : string;
}
