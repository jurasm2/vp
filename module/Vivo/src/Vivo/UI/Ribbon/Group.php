<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab Group
 */
class Group extends UI\ComponentContainer
{
    public function init()
    {
        $items = array();
        foreach($this->components as $component) {
            if($component->isVisible()) {
                $items[] = $component->getName();
            }
        }
        $this->view->components = $items;
        $this->view->title = '('.$this->getName().')';
        parent::init();
    }
}
