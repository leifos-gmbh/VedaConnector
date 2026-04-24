<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\InputFields;

use ilNumberInputGUI;
use ilObject;
use Leifos\VedaConnector\I\InputFields\RefIdNumberInterface;
use Leifos\VedaConnector\I\PluginInterface;

class RefIdNumber extends ilNumberInputGUI implements RefIdNumberInterface
{
    public function __construct(
        protected PluginInterface $plugin,
        string $a_title = "",
        string $a_postvar = ""
    ) {
        parent::__construct($a_title, $a_postvar);
    }

    public function checkInput(): bool
    {
        $parent_input_check = parent::checkInput();
        if (
            $parent_input_check &&
            !$this->inputIsCategoryRefId()
        ) {
            $this->setAlert($this->plugin->txt('tbl_settings_course_import_category_error'));
            return false;
        }
        return $parent_input_check;
    }

    protected function inputIsCategoryRefId(): bool
    {
        return ilObject::_lookupType((int) $this->getInput(), true) === 'cat';
    }
}
