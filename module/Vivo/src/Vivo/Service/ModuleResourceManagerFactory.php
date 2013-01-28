<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ModuleResourceManagerFactory
 */
class ModuleResourceManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        $resourceManagerOptions = $config['modules']['resource_manager'];
        $moduleStorageManager   = $serviceLocator->get('module_storage_manager');
        $moduleResourceManager  = new \Vivo\Module\ResourceManager\ResourceManager($moduleStorageManager,
                                        $resourceManagerOptions);
        return $moduleResourceManager;
    }
}
