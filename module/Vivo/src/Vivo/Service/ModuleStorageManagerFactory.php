<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ModuleStorageManagerFactory
 */
class ModuleStorageManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
//                'module_storage_manager'    => function(ServiceManager $sm) {
        $config                 = $serviceLocator->get('config');
        $modulePaths            = $config['modules']['module_paths'];
        $descriptorName         = $config['modules']['descriptor_name'];
        $defaultInstallPath     = $config['modules']['default_install_path'];
        $storage                = $serviceLocator->get('module_storage');
        $storageUtil            = $serviceLocator->get('storage_util');
        $remoteModule           = $serviceLocator->get('remote_module');
        $pathBuilder            = $serviceLocator->get('path_builder');
        $manager    = new \Vivo\Module\StorageManager\StorageManager($storage,
            $modulePaths,
            $descriptorName,
            $defaultInstallPath,
            $storageUtil,
            $remoteModule,
            $pathBuilder);
        return $manager;
    }
}
