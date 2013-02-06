<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab
 */
class Tab extends UI\ComponentContainer
{
    public function init() {
        $this->view->components = array_keys($this->components);
        $this->view->name = $this->getName();
        parent::init();
    }
}
