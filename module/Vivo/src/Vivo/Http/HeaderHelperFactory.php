<?php
namespace Vivo\Http;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for HeaderHelper
 */
class HeaderHelperFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return HeaderHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        return new HeaderHelper($config['response']['headers']);
    }
}
