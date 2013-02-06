<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Fieldset;
use Zend\Form\Element;

/**
 * VivoFormFieldset
 * Form fieldset view helper renders a fieldset
 */
class VivoFormFieldset extends AbstractHelper
{
    /**
     * Default view helper options
     * @var array
     */
    protected $defaultOptions   = array(
        'renderFieldsetTag'         => true,
        'renderChildFieldsetTags'   => true,
    );

    /**
     * Invoke the helper as a PhpRenderer method call
     * @param \Zend\Form\Fieldset $fieldset
     * @param array $options
     * @return string
     */
    public function __invoke(Fieldset $fieldset = null,  array $options = array())
    {
        if (!$fieldset) {
            return $this;
        }
        return $this->render($fieldset, $options);
    }

    /**
     * Renders the fieldset
     * @param \Zend\Form\Fieldset $fieldset
     * @param array $options
     * @throws Exception\UnsupportedFormItemException
     * @return string
     */
    public function render(Fieldset $fieldset, array $options = array())
    {
        $options    = array_merge($this->defaultOptions, $options);
        /** @var $formRowVh \Zend\Form\View\Helper\FormRow */
        $formRowVh      = $this->getView()->plugin('formRow');
        $translator     = $this->getView()->plugin('translate');
        $escaper        = $this->getView()->plugin('escapeHtml');
        $html           = '';
        if ($options['renderFieldsetTag']) {
            $html           .= "\n<fieldset>";
            if ($fieldset->getLabel()) {
                $label      = $escaper($translator($fieldset->getLabel()));
                $html       .= "\n<legend>" . $label. '</legend>';
            }
        }
        foreach ($fieldset as $item) {
            if ($item instanceof Fieldset) {
                //Fieldset
                $childOptions   = array(
                    'renderFieldsetTag'         => $options['renderChildFieldsetTags'],
                    'renderChildFieldsetTags'   => $options['renderChildFieldsetTags'],
                );
                $html   .= $this->render($item, $childOptions);
            } elseif ($item instanceof Element) {
                //Element
                $html   .= "\n<div class=\"form_element\">";
                $html   .= $formRowVh->render($item);
                $html   .= "\n</div>";
            } else {
                //Unsupported type
                throw new Exception\UnsupportedFormItemException(sprintf("%s: Unsupported form item type '%s'",
                    __METHOD__, get_class($item)));
            }
        }
        if ($options['renderFieldsetTag']) {
            $html           .= "\n</fieldset>";
        }
        return $html;
    }
}
