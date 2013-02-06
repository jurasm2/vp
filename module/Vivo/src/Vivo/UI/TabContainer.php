<?php
namespace Vivo\UI;

/**
 * TabContainer
 */
class TabContainer extends ComponentContainer
{
    /**
     * @var string Component / tab name.
     */
    public $selected = false;

    /**
     * whether render all tabs
     * @var boolean
     */
    private $viewAll = false;

    /**
     * Method for selecting a tab. Call method selected() on selected component.
     * @param strign $name Tab name.
     */
    public function select($name = false)
    {
        $selectedComponent = $this->components[$name];

        if ($selectedComponent instanceof TabContainerItemInterface) {
            $selectedComponent->select();
        }

        return $this->selected = $name;
    }

    /**
     * @param boolean $value
     */
    public function setViewAll($value)
    {
        $this->viewAll = (boolean) $value;
    }

    /**
     * Prepare data for view.
     * @return array
     */
    public function prepareTabs()
    {
        $tabs = array();

        foreach($this->components as $name => $component) {
            if ($component instanceOf \Vivo\UI\TabContainerItemInterface) {
                if(!$component->isDisabled()) {
                    $tab = array(
                        'name' => $name,
                        'label' => $component->getLabel()
                    );
                }
            } else {
                $tab = array(
                    'name' => $name,
                    'label' => "($name)"
                );
            }
            $tabs[] = $tab;
        }
        return $tabs;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::view()
     */
    public function view()
    {
        $this->view->tabs = $this->prepareTabs();
        $this->view->components = $keys = array_keys($this->components);
        $this->view->selected = $this->selected ?  : reset($keys);
        $this->view->viewAll = $this->viewAll;
        return parent::view();
    }

}
