<?php
namespace Vivo\UI;

use Zend\EventManager\EventManagerAwareInterface;

/**
 * Ribbon
 */
class Ribbon extends TabContainer implements EventManagerAwareInterface
{
    private $eventManager;
    
	public function view() {
        $this->view->name = $this->getName();
        $this->view->components = array_keys($this->components);
        return parent::view();
    }
    
    public function itemClick($name)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('itemName' => $name));
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
