<?php
namespace Vivo\Form\Element;

use Zend\Form\Element\DateTime as ZendDateTime;

class DateTime extends ZendDateTime
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

    public function getFilters()
    {
        return array(
            array('name' => 'Zend\Filter\StringTrim'),
            array('name' => 'Vivo\Filter\DateTime'), //TODO: set format from options
        );
    }

    protected function getValidators()
    {
        return array();
    }

    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => $this->getFilters(),
            'validators' => $this->getValidators(),
        );
    }
}
