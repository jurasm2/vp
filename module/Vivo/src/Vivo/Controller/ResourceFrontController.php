<?php
namespace Vivo\Controller;

use Vivo\CMS\Api;
use Vivo\CMS\Model\Content\File;
use Vivo\Http\HeaderHelper;
use Vivo\IO\Exception\ExceptionInterface as IOException;
use Vivo\IO\FileInputStream;
use Vivo\Module\Exception\ResourceNotFoundException as ModuleResourceNotFoundException;
use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Util;

use Zend\EventManager\EventInterface as Event;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * Controller for giving all resource files
 */
class ResourceFrontController implements DispatchableInterface,
        InjectApplicationEventInterface
{

    /**
     * @var Api\CMS
     */
    protected $cmsApi;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var SiteEvent
     */
    protected $siteEvent;

    public function dispatch(Request $request, Response $response = null)
    {
        $pathToResource = $this->event->getRouteMatch()->getParam('path');
        $source = $this->event->getRouteMatch()->getParam('source');
        try {
            if ($source === 'Vivo') {
                //it's vivo core resource
                    $resourceStream = new FileInputStream(__DIR__ . '/../../../resource/' . $pathToResource);
            } elseif ($source === 'entity') {
                //it's entity resource
                $entityPath = $this->event->getRouteMatch()->getParam('entity');
                $entity = $this->cmsApi->getSiteEntity($entityPath, $this->siteEvent->getSite());

                if ($entity instanceof File) {
                    //TODO match interface instead of the concrete class File
                    $filename = $entity->getFilename();
                }

                $resourceStream = $this->cmsApi->readResource($entity, $pathToResource);
            } else {
                //it's module resource
                $resourceStream = $this->resourceManager->readResource($source, $pathToResource);
            }
            if (!isset($filename)) {
                $filename   = pathinfo($pathToResource, PATHINFO_BASENAME);
            }
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $mimeType = Util\MIME::getType($ext);

            //set headers
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type: ' . $mimeType);
            $headers->addHeaderLine('Content-Disposition: inline; filename="' . $filename . '"');
            $this->headerHelper->setExpirationByMimeType($headers, $mimeType);

            $response->setInputStream($resourceStream);
        } catch (\Exception $e) {
            if ($e instanceof IOException ||
                    $e instanceof ModuleResourceNotFoundException ||
                    $e instanceof CMSResourceNotFoundException) {
                $response->setStatusCode(HttpResponse::STATUS_CODE_404);
            } else {
                throw $e;
            }
        }
        return $response;
    }

    /**
     * @param Api\CMS $cms
     */
    public function setCMS(Api\CMS $cmsApi) {
        $this->cmsApi = $cmsApi;
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
     * @param SiteEvent $sitEevent
     */
    public function setSiteEvent(SiteEvent $siteEvent)
    {
        $this->siteEvent = $siteEvent;
    }

    /**
     * @param HeaderHelper $headerHelper
     */
    public function setHeaderHelper(HeaderHelper $headerHelper)
    {
        $this->headerHelper = $headerHelper;
    }
}
