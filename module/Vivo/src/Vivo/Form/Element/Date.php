<?php
namespace Vivo\Form\Element;

use Zend\Form\Element\Date as ZendDate;

class Date extends ZendDate
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
