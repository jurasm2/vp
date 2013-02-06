<?php
namespace Vivo\Service\Initializer;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Default initializer for instances created by service manager.
 */
class DefaultInitializer implements InitializerInterface
{
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\InitializerInterface::initialize()
     */
    public function initialize($instance,
            ServiceLocatorInterface $serviceLocator)
    {
        //inject request
        if ($instance instanceof RequestAwareInterface) {
            $instance->setRequest($serviceLocator->get('request'));
        }
        //inject redirector
        if ($instance instanceof RedirectorAwareInterface) {
            $instance->setRedirector($serviceLocator->get('redirector'));
        }
        //inject site event
        if ($instance instanceof SiteEventAwareInterface) {
            $instance->setSiteEvent($serviceLocator->get('site_event'));
        }
    }
}
