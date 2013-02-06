<?php
namespace Vivo\Form\Element;

use Zend\Form\Element\Time as ZendTime;

class Time extends ZendTime
{
    /**
     * @see \Zend\Form\Element::setOptions()
     */
    public function setOptions($options)
    {
        if(isset($options['format'])) {
            $this->setFormat($options['format']);
            unset($options['format']);
        }

        parent::setOptions($options);
        return $this;
    }
}
