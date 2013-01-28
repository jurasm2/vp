<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * RemoteModuleFactory
 */
class RemoteModuleFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        $descriptorName         = $config['modules']['descriptor_name'];
        $storageFactory         = $serviceLocator->get('storage_factory');
        $pathBuilder            = $serviceLocator->get('path_builder');
        $remoteModule           = new \Vivo\Module\StorageManager\RemoteModule($storageFactory,
                                        $descriptorName,
                                        $pathBuilder);
        return $remoteModule;
    }
}
