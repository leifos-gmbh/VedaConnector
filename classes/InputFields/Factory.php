<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\InputFields;

use Leifos\VedaConnector\I\InputFields\FactoryInterface;
use Leifos\VedaConnector\I\InputFields\RefIdNumberInterface;
use Leifos\VedaConnector\I\PluginInterface;

class Factory implements FactoryInterface
{
    public function __construct(
        protected PluginInterface $plugin
    ) {
    }

    public function refIdNumber(
        string $title = "",
        string $post_var = ""
    ): RefIdNumberInterface
    {
        return new RefIdNumber(
            $this->plugin,
            $title,
            $post_var
        );
    }
}
