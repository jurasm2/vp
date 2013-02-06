<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Form as ZfForm;
use Zend\Form\Fieldset;
use Zend\Form\Element;


/**
 * VivoForm
 * Form view helper renders complete form
 */
class VivoForm extends AbstractHelper
{
    /**
     * Default view helper options
     * @var array
     */
    protected $defaultOptions   = array(
        'renderFormTag'     => true,
    );

    /**
     * Invoke the helper as a PhpRenderer method call
     * @param \Zend\Form\Form $form
     * @param array $options
     * @return VivoForm
     */
    public function __invoke(ZfForm $form = null, array $options = array())
    {
        if (!$form) {
            return $this;
        }
        return $this->render($form, $options);
    }

    /**
     * Renders the form
     * @param \Zend\Form\Form $form
     * @param array $options
     * @return string
     */
    public function render(ZfForm $form, array $options = array())
    {
        $options    = array_merge($this->defaultOptions, $options);
        /** @var $formVh \Zend\Form\View\Helper\Form */
        $formVh             = $this->getView()->plugin('form');
        /** @var $vivoFormFieldsetVh VivoFormFieldset */
        $vivoFormFieldsetVh = $this->getView()->plugin('vivoFormFieldset');
        $html               = '';
        if ($options['renderFormTag']) {
            $html               .= $formVh->openTag($form);
        }
        $fieldsetOptions    = array(
            'renderFieldsetTag'         => false,
            'renderChildFieldsetTags'   => true,
        );
        $html               .= $vivoFormFieldsetVh->render($form, $fieldsetOptions);
        if ($options['renderFormTag']) {
            $html               .= $formVh->closeTag();
        }
        return $html;
    }
}
