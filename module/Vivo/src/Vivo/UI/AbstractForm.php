<?php
namespace Vivo\UI;

use Vivo\Service\Initializer\RequestAwareInterface;
use Vivo\Service\Initializer\RedirectorAwareInterface;
use Vivo\Util\Redirector;

use Zend\Form\FormInterface;
use Zend\Form\Form as ZfForm;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\RequestInterface;

/**
 * Form
 * Base abstract Vivo Form
 */
abstract class AbstractForm extends ComponentContainer implements RequestAwareInterface, RedirectorAwareInterface
{
    /**
     * @var ZfForm
     */
    private $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Redirector instance
     * @var Redirector
     */
    protected $redirector;

    /**
     * Has the data been loaded from request?
     * @var bool
     */
    protected $dataLoaded           = false;

    /**
     * When set to true, the form will be automatically prepared in view() method
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoPrepareForm      = true;

    /**
     * When set to true, CSRF protection will be automatically added to the form
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoAddCsrf          = true;

    /**
     * When set to true, data will be automatically loaded to the form from request
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoLoadFromRequest  = true;

    /**
     * TTL for CSRF token
     * Redefine in descendant if necessary
     * @var int|null
     */
    protected $csrfTimeout          = 300;

    public function init()
    {
        parent::init();
        //Load form data from request
        if ($this->autoLoadFromRequest) {
            $this->loadFromRequest();
        }
    }

    /**
     * Returns view model or string to display directly
     * @return \Zend\View\Model\ModelInterface|string
     */
    public function view()
    {
        $form   = $this->getForm();
        //Prepare the form
        if ($this->autoPrepareForm) {
            $form->prepare();
        }
        //Set form to view
        $this->view->form   = $form;
        return parent::view();
    }

    /**
     * Get ZF form
     * @return ZfForm
     */
    protected function getForm()
    {
        if($this->form == null) {
            $this->form = $this->doGetForm();
            if ($this->autoAddCsrf) {
                //Add CSRF field
                $this->form->add(array(
                    'type'      => 'Vivo\Form\Element\Csrf',
                    'name'      => 'csrf',
                    'options'   => array(
                        'csrf_options'  => array(
                            'timeout'   => $this->csrfTimeout,
                        ),
                    ),
                ));
            }
        }
        return $this->form;
    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return ZfForm
     */
    abstract protected function doGetForm();

    public function setRequest(RequestInterface $request)
    {
        $this->request  = $request;
    }

    /**
     * Injects redirector
     * @param \Vivo\Util\Redirector $redirector
     * @return void
     */
    public function setRedirector(Redirector $redirector)
    {
        $this->redirector   = $redirector;
    }

    /**
     * Loads data into the form from the HTTP request
     * Loads GET as well as POST data (POST wins)
     */
    public function loadFromRequest()
    {
        if ($this->dataLoaded) {
            return;
        }
        $data   = $this->request->getQuery()->toArray();
        $data   = array_merge($data, $this->request->getPost()->toArray());

        //Unset act field to prevent mix up with an unrelated act field
        unset($data['act']);

        $form   = $this->getForm();
        $form->setData($data);
        $this->dataLoaded   = true;
    }

    /**
     * Validates a single form field
     * Facilitates single field AJAX validations
     * @param string $fieldName in array notation (eg 'fieldset1[fieldset2][fieldname]')
     * @param string $fieldValue Value being tested
     * @param array $messages Validation messages are returned in this array
     * @return boolean
     */
    public function isFieldValid($fieldName, $fieldValue, array &$messages)
    {
        $form       = $this->getForm();
        $fieldSpec  = $this->getFormDataFromArrayNotation($fieldName);
        $data       = $this->getFormDataFromArrayNotation($fieldName, $fieldValue);
        $form->setValidationGroup($fieldSpec);
        $form->setData($data);
        //Store the current bind on validate flag setting and set it to manual
        $bindOnValidateFlag = $form->bindOnValidate();
        $form->setBindOnValidate(FormInterface::BIND_MANUAL);
        //Validate the field
        $valid  = $form->isValid();
        //Restore the original bind on validate setting
        $form->setBindOnValidate($bindOnValidateFlag);
        if (!$valid) {
            $formMessages   = $form->getMessages();
            $fieldMessages  = $this->getFieldMessages($formMessages, $fieldName);
            $messages   = array_merge($messages, $fieldMessages);
        }
        //Reset the validation group to whole form
        $form->setValidationGroup(FormInterface::VALIDATE_ALL);
        return $valid;
    }

    /**
     * Returns field specification or field data from an array notation of a field
     * getFormDataFromArrayNotation('fieldset1[fieldset2][fieldname]', 'valueX') returns
     * array(
     *      'fieldset1' => array(
     *          'fieldset2' => array('fieldname' => 'valueX')
     *      )
     * )
     * getFormDataFromArrayNotation('fieldset1[fieldset2][fieldname]') returns
     * array(
     *      'fieldset1' => array(
     *          'fieldset2' => array('fieldname')
     *      )
     * )
     * For 'fieldname' returns array('fieldname')
     * @param string $arrayNotation Field name in array notation
     * @param string $value Field value
     * @return array
     */
    protected function getFormDataFromArrayNotation($arrayNotation, $value = null)
    {
        $parts          = $this->getPartsFromArrayNotation($arrayNotation);
        $fieldName      = array_pop($parts);
        if (is_null($value)) {
            $fieldSpec      = array($fieldName);
        } else {
            $fieldSpec      = array($fieldName => $value);
        }
        $reversed   = array_reverse($parts);
        foreach ($reversed as $fieldset) {
            $fieldSpec  = array($fieldset => $fieldSpec);
        }
        return $fieldSpec;
    }

    /**
     * Returns error messages related to the specified field
     * @param array $messages Error messages for the whole form
     * @param string $fieldName In array notation ('fieldset1[fieldset2][fieldname]')
     * @return array
     */
    protected function getFieldMessages(array $messages, $fieldName)
    {
        $parts  = $this->getPartsFromArrayNotation($fieldName);
        foreach ($parts as $part) {
            if (!array_key_exists($part, $messages)) {
                return array();
            }
            $messages   = $messages[$part];
        }
        return $messages;
    }

    /**
     * Returns array of parts exploded from array notation
     * For 'fieldset1[fieldset2][fieldname]' returns array('fieldset1', 'fieldset2', 'fieldname')
     * @param string $arrayNotation
     * @return array
     */
    protected function getPartsFromArrayNotation($arrayNotation)
    {
        $arrayNotation  = str_replace(']', '', $arrayNotation);
        $parts          = explode('[', $arrayNotation);
        return $parts;
    }
}
