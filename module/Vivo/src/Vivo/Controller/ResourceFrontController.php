<?php
namespace Vivo\Controller;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Content\File;
use Vivo\Controller\Exception;
use Vivo\IO\Exception\ExceptionInterface as IOException;
use Vivo\IO\FileInputStream;
use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Module\Exception\ResourceNotFoundException as ModuleResourceNotFoundException;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Util;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\EventManager\EventInterface as Event;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\Response as HttpResponse;

/**
 * Controller for giving all resource files
 */
class ResourceFrontController implements DispatchableInterface,
        InjectApplicationEventInterface
{

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     *
     * @var SiteEvent
     */
    protected $siteEvent;

    public function dispatch(Request $request, Response $response = null)
    {
        $pathToResource = $this->event->getRouteMatch()->getParam('path');
        $source = $this->event->getRouteMatch()->getParam('source');
        try {
            if ($source === 'Vivo') {
                //it's vivo core resource - the core resources should be moved into own module
                    $resourceStream = new FileInputStream(
                            __DIR__ . '/../../../resource/' . $pathToResource);
            } elseif ($source === 'entity') {
                //it's entity resource
                $entityPath = $this->event->getRouteMatch()->getParam('entity');
                $entity = $this->cms->getSiteEntity($entityPath, $this->siteEvent->getSite());

                if ($entity instanceof File) {
                    //TODO match interface instead of the concrete class File
                    $filename = $entity->getFilename();
                }

                $resourceStream = $this->cms->readResource($entity, $pathToResource);
            } else {
                //it's module resource
                $resourceStream = $this->resourceManager
                        ->readResource($source, $pathToResource);
            }
            if (!isset($filename)) {
                $filename   = pathinfo($pathToResource, PATHINFO_BASENAME);
            }
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $mimeType = Util\MIME::getType($ext);
            $response->getHeaders()->addHeaderLine('Content-Type: ' . $mimeType)
                    ->addHeaderLine(
                            'Content-Disposition: inline; filename="' . $filename . '"');

            $response->setInputStream($resourceStream);
        } catch (\Exception $e) {
            if ($e instanceof IOException ||
                    $e instanceof ModuleResourceNotFoundException ||
                    $e instanceof IOException ||
                    $e instanceof CMSResourceNotFoundException) {
                $response->setStatusCode(HttpResponse::STATUS_CODE_404);
            } else {
                throw $e;
            }
        }
        return $response;
    }

    /**
     * @param CMS $cms
     */
    public function setCMS(CMS $cms) {
        $this->cms = $cms;
    }

    /* (non-PHPdoc)
     * @see Zend\Mvc.InjectApplicationEventInterface::setEvent()
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /* (non-PHPdoc)
     * @see Zend\Mvc.InjectApplicationEventInterface::getEvent()
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param ResourceManager $resourceManager
     */
    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param Site $site
     */
    public function setSiteEvent(SiteEvent $siteEvent)
    {
        $this->siteEvent = $siteEvent;
    }
}
