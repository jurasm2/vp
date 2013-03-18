<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\AbstractForm;
use Vivo\UI\Alert;
use Vivo\Form;
use Vivo\Backend\UI\Form\Fieldset\EntityEditor as EntityEditorFieldset;
use Vivo\CMS\AvailableContentsProvider;
use Vivo\CMS\Api\DocumentInterface as DocumentApiInterface;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\ContentContainer;
use Vivo\Util\RedirectEvent;
use Vivo\LookupData\LookupDataManager;
use Vivo\Service\Initializer\TranslatorAwareInterface;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\I18n\Translator\Translator;

class Editor extends AbstractForm implements TranslatorAwareInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $sm;

    /**
     * Document API
     * @var DocumentApiInterface
     */
    protected $documentApi;

    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @var LookupDataManager
     */
    protected $lookupDataManager;

    /**
     * @var \Vivo\CMS\AvailableContentsProvider
     */
    private $availableContentsProvider;

    /**
     * @var \Vivo\CMS\Model\Folder
     */
    protected $entity;

    /**
     * @var array
     */
    private $availableContents = array();

    /**
     * @var \Vivo\UI\Alert
     */
    private $alert;

    /**
     * Translator
     * @var Translator
     */
    protected $translator;

    /**
     * TTL for CSRF token
     * Redefine in descendant if necessary
     * @var int|null
     */
    protected $csrfTimeout          = 3600;

    /**
     * Constructor
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\LookupData\LookupDataManager $lookupDataManager
     * @param \Vivo\CMS\Api\DocumentInterface $documentApi
     * @param \Vivo\CMS\AvailableContentsProvider $availableContentsProvider
     */
    public function __construct(
        \Zend\ServiceManager\ServiceManager $sm,
        \Vivo\Metadata\MetadataManager $metadataManager,
        LookupDataManager $lookupDataManager,
        DocumentApiInterface $documentApi,
        AvailableContentsProvider $availableContentsProvider)
    {
        $this->sm = $sm;
        $this->metadataManager = $metadataManager;
        $this->lookupDataManager    = $lookupDataManager;
        $this->documentApi = $documentApi;
        $this->availableContentsProvider = $availableContentsProvider;
    }

    public function init()
    {
        $this->entity = $this->getParent()->getEntity();

        $this->getForm()->bind($this->entity);

        try {
            $this->getComponent('resourceEditor')->setEntity($this->entity);
        }
        catch (\Vivo\UI\Exception\ComponentNotExists $e) { }

        parent::init();
        $this->initForm();
    }

    protected function initForm()
    {
        // Load avalilable contents
        $this->availableContents = $this->availableContentsProvider->getAvailableContents($this->entity);

        // Editor tabs
        $containers = array();
        if($this->entity instanceof Document) {
            try {
                $containers = $this->documentApi->getContentContainers($this->entity);
            }
            catch(\Vivo\Repository\Exception\PathNotSetException $e) {

            }
        }
        $count = count($containers);
        foreach ($containers as $index => $contentContainer) {
            $this->contentTab->addComponent($this->createContentTab($contentContainer), "content_$index");
        }

        $this->contentTab->addComponent($this->createContentTab(new ContentContainer()), "content_$count");
    }

    /**
     * @param Alert $alert
     */
    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @param \Vivo\UI\TabContainer $tab
     */
    public function setTabContainer(\Vivo\UI\TabContainer $tab)
    {
        $this->addComponent($tab, 'contentTab');
    }

    /**
     * @param \Vivo\Backend\UI\Explorer\Editor\Resource $editor
     */
    public function setResourceEditor(Editor\Resource $editor)
    {
        $this->addComponent($editor, 'resourceEditor');
    }

    /**
     * Injects translator
     * @param \Zend\I18n\Translator\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));
        $lookupData = $this->lookupDataManager->injectLookupData($metadata, $this->entity);
        $action = $this->request->getUri()->getPath();

        // Entity fieldset
        $editorFieldset = new EntityEditorFieldset('entity', $lookupData);
        $editorFieldset->setHydrator(new ClassMethodsHydrator(false));
        $editorFieldset->setOptions(array('use_as_base_fieldset' => true));

        // Buttons
        $buttonsFieldset = new Form\Fieldset('buttons');
        $buttonsFieldset->add(array(
            'name' => 'save',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'class' => 'btn',
            ),
        ));

        $form = new Form\Form('entity-' . $this->entity->getUuid());
        $form->setAttribute('action', $action);
        $form->setAttribute('method', 'post');
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));

        $form->add($editorFieldset);
        $form->add($buttonsFieldset);

        return $form;
    }

    /**
     * @param \Vivo\CMS\Model\ContentContainer $contentContainer
     * @return \Vivo\Backend\UI\Explorer\Editor\ContentTab
     */
    private function createContentTab(ContentContainer $contentContainer)
    {
        $tab = $this->sm->create('Vivo\Backend\UI\Explorer\Editor\ContentTab');
        $tab->setContentContainer($contentContainer);
        $tab->setAvailableContents($this->availableContents);

        return $tab;
    }

    /**
     * @param string $message
     * @param string $type
     */
    protected function addAlertMessage($message, $type)
    {
        if($this->alert) {
            $this->alert->addMessage($message, $type);
        }
    }

    /**
     * Save action.
     * @throws \Exception
     */
    public function save()
    {
        $success = true;
        $form = $this->getForm();

        if ($form->isValid()) {
            $this->entity = $this->documentApi->saveDocument($this->entity);
        }
        else {
            $success = false;
            $message = $this->translator->translate("Document data is not valid");
            $this->alert->addMessage($message, Alert::TYPE_ERROR);
        }

        $success = $success && $this->saveContents();

        if($success) {
            $this->events->trigger(new RedirectEvent());
        }
    }

    /**
     * @return boolean
     */
    private function saveContents()
    {
        $success = $this->saveProcess();

        if($success) {
            $this->addAlertMessage('Saved...', Alert::TYPE_SUCCESS);
        }
        else {
            $this->addAlertMessage('Error...', Alert::TYPE_ERROR);
        }

        return $success;
    }

    /**
     * @return boolean
     */
    protected function saveProcess()
    {
        $success = true;

        /* @var $component \Vivo\Backend\UI\Explorer\Editor\ContentTab */
        $component = $this->getComponent('contentTab')->getSelectedComponent();

        $success = $success && $component->save();

        if($success) {
            $this->contentTab->removeAllComponents();
            $this->init();

            foreach ($this->contentTab->getComponents() as $component) {
                $component->initForm();
            }
        }

        return $success;
    }
}
