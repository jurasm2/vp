<?php
namespace Vivo\Form;

use Vivo\Form\Factory;
use Zend\Form\Form as ZendForm;

class Form extends ZendForm
{
    public function getFormFactory()
    {
        if (null === $this->factory) {
            $this->setFormFactory(new Factory());
        }

        return $this->factory;
    }
}
