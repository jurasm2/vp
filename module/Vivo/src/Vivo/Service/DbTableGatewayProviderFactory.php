<?php
namespace Vivo\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * DbTableGatewayProviderFactory
 */
class DbTableGatewayProviderFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $dbTableNameProvider DbTableNameProvider */
        $dbTableNameProvider    = $serviceLocator->get('db_table_name_provider');
        $service                = new DbTableGatewayProvider();
        $tableNames             = $dbTableNameProvider->getTableNames();
        /** @var $dbProviderCore DbProvider */
        $dbProviderCore         = $serviceLocator->get('db_provider_core');
        $zdba                   = $dbProviderCore->getZendDbAdapter();
        foreach ($tableNames as $symbolic => $real) {
            $service->add($symbolic, $zdba, $real);
        }
        //Register core tables
        return $service;
    }
}
