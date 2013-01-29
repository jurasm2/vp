<?php
namespace Vivo\UI;

/**
 * Interface for UI components.
 */
interface ComponentInterface
{
    /**
     * @return void
     */
    public function init();

    /**
     * Returns view model or string to display directly
     * @return \Zend\View\Model\ModelInterface|string
     */
    public function view();

    /**
     * @return void
     */
    public function done();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return ComponentContainerInterface
     */
    public function getParent();
}
