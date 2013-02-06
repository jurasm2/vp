<?php
namespace Vivo;

use Vivo\Http\Filter\OutputFilterListener;
use Vivo\View\Helper as ViewHelper;
use Vivo\Service\Exception;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    /**
     * Module bootstrap method.
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        //initialize logger
        $logger = $e->getApplication()->getServiceManager()->get('logger');

        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm     = $e->getApplication()->getServiceManager();
        /* @var $sm ServiceManager */
        $config = $sm->get('config');

        //Attach a listener to set up the SiteManager object
        $runSiteManagerListener = $sm->get('run_site_manager_listener');
        $runSiteManagerListener->attach($eventManager);

        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array ($this, 'registerTemplateResolver'));
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array ($this, 'registerViewHelpers'));

        $filterListener = $sm->get('Vivo\Http\Filter\OutputFilterListener');
        $filterListener->attach($eventManager);
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php'; //main vivo config
        $config['cms'] = include __DIR__ . '/config/cms.config.php'; //CMS config
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Register template resolver.
     * @param MvcEvent $e
     */
    public function registerTemplateResolver(MvcEvent $e)
    {
        $sm = $e->getTarget()->getServiceManager();
        $sm->get('viewresolver')->attach($sm->get('template_resolver'));
    }

    /**
     * Registers view helpers to the view helper manager.
     * @param MvcEvent $e
     */
    public function registerViewHelpers($e) {
        $app          = $e->getTarget();
        $serviceLocator      = $app->getServiceManager();
        /* @var $plugins \Zend\View\HelperPluginManager */
        $plugins      = $serviceLocator->get('view_helper_manager');
        $plugins->setFactory('resource', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Resource($serviceLocator->get('cms'));
            return $helper;
        });
        $plugins->setFactory('document', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Document($serviceLocator->get('cms'));
            return $helper;
        });
    }

    public function getServiceConfig()
    {
        return array();
    }

    public function getConsoleBanner(Console $console)
    {
        return "Vivo 2 CLI\n";
    }

    public function getConsoleUsage(Console $console)
    {
        return array('Available commands:',
                array ('indexer', 'Perform operations on indexer..'),
                array ('info','Show information about CMS instance.'),
                array ('module', 'Manage modules.'),
                array ('repository', 'Administer the repository.'),
                array ('cms', 'CMS functions.'),
                array ('setup', 'System setup'),
        );
    }
}
