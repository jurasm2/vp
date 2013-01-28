<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * ModuleManagerFactoryFactory
 */
class ModuleManagerFactoryFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        $modulePaths            = $config['modules']['module_paths'];
        $moduleStreamName       = $config['modules']['stream_name'];
        $application            = $serviceLocator->get('application');
        /* @var $application \Zend\Mvc\Application */
        $appEvents              = $application->getEventManager();
        $moduleManagerFactory   = new \Vivo\Module\ModuleManagerFactory($modulePaths, $moduleStreamName, $appEvents);
        return $moduleManagerFactory;
    }
}
