<?php
namespace Vivo\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Factory for ResourceFrontController
 */
class ResourceFrontControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $controller = new ResourceFrontController();
        $controller->setCMS($sm->get('Vivo\CMS\Api\CMS'));
        $controller->setResourceManager($sm->get('module_resource_manager'));
        $controller->setSiteEvent($sm->get('site_event'));
        $controller->setHeaderHelper($sm->get('Vivo\Http\HeaderHelper'));
        return $controller;
    }
}
