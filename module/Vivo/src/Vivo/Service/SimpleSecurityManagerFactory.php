<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * SimpleSecurityManagerFactory
 */
class SimpleSecurityManagerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Vivo\CMS\Security\Simple\Manager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config             = $serviceLocator->get('config');
        $secManConfig       = $config['security_manager'];
        /** @var $siteEvent \Vivo\SiteManager\Event\SiteEventInterface */
        $siteEvent          = $serviceLocator->get('site_event');
        $secManOptions      = array(
            'security_domain'   => $siteEvent->getSite()->getDomain(),
        );
        $secManOptions      = array_merge($secManOptions, $secManConfig['options']);
        $sessionManager     = $serviceLocator->get('session_manager');
        $securityManager    = new \Vivo\CMS\Security\Simple\Manager($sessionManager, $secManOptions);
        return $securityManager;
    }
}
