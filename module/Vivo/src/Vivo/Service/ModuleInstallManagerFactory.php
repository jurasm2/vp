<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ModuleInstallManagerFactory
 */
class ModuleInstallManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        $moduleStorageManager   = $serviceLocator->get('module_storage_manager');
        $cms                    = $serviceLocator->get('cms');
        $dbProviderFactory      = $serviceLocator->get('db_provider_factory');
        $options                = array();
        if (!isset($config['modules']['default_db_source'])) {
            throw new Exception\ConfigException(
                sprintf("%s: '[modules][default_db_source]' key missing in config", __METHOD__));
        }
        $options['default_db_source']   = $config['modules']['default_db_source'];
        $moduleInstallManager   = new \Vivo\Module\InstallManager\InstallManager($moduleStorageManager,
            $cms,
            $dbProviderFactory,
            $options);
        return $moduleInstallManager;
    }
}
