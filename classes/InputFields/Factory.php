<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\InputFields;

use Leifos\VedaConnector\I\InputFields\FactoryInterface;
use Leifos\VedaConnector\I\InputFields\RefIdNumberInterface;
use Leifos\VedaConnector\I\Lang\FactoryInterface as LangFactoryInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected LangFactoryInterface $lang_factory
    ) {
    }

    public function refIdNumber(
        string $title = "",
        string $post_var = ""
    ): RefIdNumberInterface
    {
        return new RefIdNumber(
            $this->lang_factory->handler(),
            $title,
            $post_var
        );
    }
}
