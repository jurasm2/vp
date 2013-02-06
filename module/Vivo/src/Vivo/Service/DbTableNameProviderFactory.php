<?php
namespace Vivo\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DbTableNameProviderFactory
 */
class DbTableNameProviderFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config                 = $serviceLocator->get('config');
        if (!isset($config['setup']['db']['table_names'])) {
            throw new Exception\ConfigException(
                sprintf("%s: Config key ['setup']['db']['table_names'] missing", __METHOD__));
        }
        $tableNames             = $config['setup']['db']['table_names'];
        $dbTableNameProvider    = new DbTableNameProvider($tableNames);
        return $dbTableNameProvider;
    }
}
