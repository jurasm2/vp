<?php
namespace Vivo\Form;

use ArrayAccess;
use Traversable;
use Zend\Form\Factory as ZendFactory;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class Factory extends ZendFactory
{
    /**
     * Create an element based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Element class to use; defaults to \Zend\Form\Element
     * - name: what name to provide the element, if any
     * - options: an array, Traversable, or ArrayAccess object of element options
     * - attributes: an array, Traversable, or ArrayAccess object of element
     *   attributes to assign
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @return ElementInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid element type
     */
    public function createElement($spec)
    {
        $spec = $this->validateSpecification($spec, __METHOD__);

        $type       = isset($spec['type'])       ? $spec['type']       : 'Vivo\Form\Element';
        $name       = isset($spec['name'])       ? $spec['name']       : null;
        $options    = isset($spec['options'])    ? $spec['options']    : null;
        $attributes = isset($spec['attributes']) ? $spec['attributes'] : null;

        $element = new $type();
        if (!$element instanceof ElementInterface) {
            throw new Exception\DomainException(sprintf(
                    '%s expects an element type that implements Zend\Form\ElementInterface; received "%s"',
                    __METHOD__,
                    $type
            ));
        }

        if ($name !== null && $name !== '') {
            $element->setName($name);
        }
// echo __METHOD__. " $type: $name\n";
        if($type == 'Vivo\Form\Element\DateTime' && empty($options['format'])) {
            $options['format'] = 'Y.m.d'; //TODO
        }

        if (is_array($options) || $options instanceof Traversable || $options instanceof ArrayAccess) {
            $element->setOptions($options);
        }

        if (is_array($attributes) || $attributes instanceof Traversable || $attributes instanceof ArrayAccess) {
            $element->setAttributes($attributes);
        }

        return $element;
    }
}
