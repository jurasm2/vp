<?php
namespace Vivo\UI\Ribbon;

use Vivo\UI;

/**
 * Ribbon Tab Group Item
 */
class Item extends UI\Component implements UI\TabContainerItemInterface, UI\RibbonItemInterface
{

    private $icon;
    private $ribbon;
    private $visible = true;
    private $active = false;
    private $enabled = true;

    public function __construct($name, $label, $icon, $ribbon = NULL)
    {
        $this->name = $name;
        $this->label = $label;
        $this->icon = $icon;
        $this->ribbon = $ribbon;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible($visible)
    {
        $this->visible = (bool) $visible;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    function click()
    {
        $this->ribbon->itemClick($this->getName());
    }

    public function select()
    {
        $this->setActive(true);
    }

    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    public function init() {
        $this->view->name = $this->getName();
        $this->view->icon = $this->getIcon();
        $this->view->enabled = $this->isEnabled();
    }
}