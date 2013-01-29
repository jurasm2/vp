<?php
namespace Vivo\CMS;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Exception\Exception;
use Vivo\CMS\Exception\LogicException;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\Site;
use Vivo\CMS\UI\InjectModelInterface;
use Vivo\CMS\UI\Content\Layout;
use Vivo\CMS\UI\Content\RawComponentInterface;
use Vivo\UI\ComponentContainer;
use Vivo\UI\ComponentInterface;

use Zend\Di\Di;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * ComponentFactory is responsible for instantiating UI components for CMS documents and resolving it's dependencies.
 */
class ComponentFactory implements EventManagerAwareInterface
{

    /**
     * @var \Vivo\CMS
     */
    private $cms;

    /**
     * @var \Zend\Di\Di
     */
    private $di;

    /**
     * @var ComponentResolver
     */
    private $resolver;

    /**
     *
     * @var Site
     */
    private $site;

    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    private $eventManager;

    /**
     * @param CMS $cms
     * @param Di $di
     */
    public function __construct(ServiceManager $sm, Di $di, CMS $cms,
            Site $site)
    {
        $this->cms = $cms;
        $this->sm = $sm;
        $this->di = $di;
        $this->site = $site;
    }

    /**
     * Returns root UI component for the given document.
     *
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getRootComponent(Document $document)
    {
        $root = $this->createComponent('Vivo\CMS\UI\Root');
        $component = $this->getFrontComponent($document);
        if ($component instanceof RawComponentInterface) {
            $root->setMain($component);
        } else {
            $page = $this->createComponent('Vivo\UI\Page');
            $page->setMain($component);
            $root->setMain($page);
        }
        return $root;
    }

    /**
     * Returns front component for the given document.
     *
     * @param Document $document
     * @param array $parameters (Disable Layout)
     * @return \Vivo\UI\Component
     */
    public function getFrontComponent(Document $document, $parameters = array())
    {
        $contents = $this->cms->getPublishedContents($document);

        if (count($contents) > 1) {
            $frontComponent = $this
                    ->createComponent('Vivo\UI\ComponentContainer');
            $i = 1;
            foreach ($contents as $content) {
                $cc = $this->getContentFrontComponent($content, $document);
                $frontComponent->addComponent($cc, 'content' . $i++);
            }

        } elseif (count($contents) === 1) {
            $frontComponent = $this
                    ->getContentFrontComponent(reset($contents), $document);
        } else {
            throw new Exception(
                    sprintf("%s: Document '%s' hasn't any published content.",
                            __METHOD__, $document->getPath()));
        }

        if ($frontComponent instanceof RawComponentInterface) {
            return $frontComponent;
        }

        if (!isset($parameters['noLayout']) || !$parameters['noLayout'] == true) {
            if ($layoutPath = $document->getLayout()) {
                $layout = $this->cms->getSiteDocument($layoutPath, $this->site);
                $panels = $this->getDocumentLayoutPanels($document);
                $frontComponent = $this
                        ->applyLayout($layout, $frontComponent, $panels);
            }
        }
        return $frontComponent;
    }

    /**
     * Wraps the UI component to Layout.
     *
     * @param Document $layout
     * @param Component $component
     * @return \Vivo\UI\Component
     */
    public function applyLayout(Document $layout, ComponentInterface $component,
            $panels = array())
    {
        $layoutComponent = $this->getFrontComponent($layout);

        if (!$layoutComponent instanceof Layout) {
            //this is usualy caused when the document hasn't layout content or has more then one content
            throw new LogicException(
                    sprintf(
                            "%s: Front component for layout must be instance of 'Vivo\CMS\UI\Content\Layout', '%s' given.",
                            __METHOD__, get_class($layoutComponent)));
        }

        $layoutComponent->setMain($component);
        $layoutPanels = $layoutComponent->getLayoutPanels();

        //document could override only panels that are defined in layout, other panels are ignored
        //TODO log warning when document tries to set panel that is not defined in layout
        $mergedPanels = array();
        foreach ($layoutPanels as $name => $panel) {
            $parts = explode('#', $name);
            if (count($parts) == 2
                    && $this->cms->getEntityUrl($layout) == $parts[0]) {
                $name = $parts[1];
            }

            if (isset($layoutPanels[$name])) {
                $mergedPanels[$name] = isset($panels[$name]) ? $panels[$name]
                        : $layoutPanels[$name];
            }
        }

        foreach ($mergedPanels as $name => $path) {
            if ($path == '') {
                //if panel is not defined we use 'layout_empty_panel' component
                $panelComponent = $this->createComponent('layout_empty_panel');

            } else {
                $panelDocument = $this->cms->getSiteDocument($path, $this->site);
                $panelComponent = $this->getFrontComponent($panelDocument);
            }
            $layoutComponent->addComponent($panelComponent, $name);
        }

        $layoutDocumentPanels = $layout->getLayoutPanels();
        $panels = array_merge($layoutDocumentPanels, $panels);

        if ($parentLayout = $this->cms->getParent($layout)) {
            if ($parentLayout instanceof Document) {
                if ($component = $this
                        ->applyLayout($parentLayout, $layoutComponent, $panels)) {
                    $layoutComponent = $component;
                }
            }
        }
        return $layoutComponent;
    }

    /**
     * Returns panels for document and its parents.
     *
     * @param Document $document
     * @todo this should be cached
     */
    public function getDocumentLayoutPanels(Document $document)
    {
        $panels = array();
        while ($document instanceof Document) {
            $panels = array_merge($document->getLayoutPanels(), $panels);
            $document = $this->cms->getParent($document);
        }
        return $panels;
    }

    /**
     * Instantiates front UI component for the given content.
     *
     * @param Content $content
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getContentFrontComponent(Content $content,
            Document $document)
    {
        if ($content instanceof \Vivo\CMS\Model\Content\Link) {
            $linkedDocument = $this->cms
                    ->getSiteDocument($content->getRelPath(), $this->site);
            return $this
                    ->getFrontComponent($linkedDocument,
                            array('noLayout' => true));
        }

        $className = $this->resolver->resolve($content);
        /* @var $component \Vivo\UI\Component */
        $component = $this->createComponent($className);
        if ($component instanceof InjectModelInterface) {
            //TODO how to properly inject document and content
            $component->setContent($content);
            $component->setDocument($document);
        }
        if ($content instanceof Content\ProvideTemplateInterface) {
            $component->getView()->setTemplate($content->getTemplate());
        }
        return $component;
    }

    /**
     * Create new instance of component.
     *
     * If service manager can create the component, SM is used. Otherwise DI is used.
     * @param string $name
     * @return \Vivo\UI\Component
     */
    public function createComponent($name)
    {
        if ($this->sm->has($name, false)) {
            $component = $this->sm->create($name);
            $type = 'ServiceManager';
        } else {
            $component = $this->di->newInstance($name, array(), false);
            $type = 'DI';
        }

        $message = "Created component '" . get_class($component) . "' using $type.";
        $this->eventManager->trigger('log', $this, array ('message' => $message));

        return $component;
    }

    /**
     * Instantiates editor UI component for the given content.
     *
     * @param Content $content
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getEditorComponent(Content $content, Document $document)
    {
        //TODO implement
    }

    /**
     * @param ComponentResolver $resolver
     */
    public function setResolver(ComponentResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
