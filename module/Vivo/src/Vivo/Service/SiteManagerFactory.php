<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * SiteManagerFactory
 */
class SiteManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        $coreModules            = $config['modules']['core_modules'];
        $siteEvents             = $serviceLocator->get('event_manager');//new \Zend\EventManager\EventManager();
        $siteEvent              = $serviceLocator->get('site_event');
        $routeParamHost         = 'host';
        $moduleManagerFactory   = $serviceLocator->get('module_manager_factory');
        $moduleStorageManager   = $serviceLocator->get('module_storage_manager');
        $cms                    = $serviceLocator->get('cms');
        $moduleResourceManager  = $serviceLocator->get('module_resource_manager');
        $siteManager            = new \Vivo\SiteManager\SiteManager($siteEvents,
            $siteEvent,
            $routeParamHost,
            $moduleManagerFactory,
            $coreModules,
            $moduleStorageManager,
            $cms,
            $serviceLocator,
            $moduleResourceManager);
        return $siteManager;
    }
}
