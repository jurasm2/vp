<?php
/**
 * Main Vivo config, this config can not be overwritten by sites, and modules.
 */
return array(
    'router' => array(
        'routes' => array(
            'vivo' => array(
                //only add hostname to routermatch
                'type' => 'Vivo\Router\Hostname',
                'may_terminate' => false,
                'child_routes' => array(

                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/((?<path>.*)/)?',
                            'spec'    => '/%path%/',
                            'defaults' => array(
                                'controller' => 'cms_front_controller',
                                'path' => '',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'query' => array(
                                'type' => 'Query',
                            ),
                        ),
                    ),

                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),

                    'resource_entity' => array(
                            'type' => 'Zend\Mvc\Router\Http\Regex',
                            'options' => array(
                                    'regex'    => '/\.entity(?<entity>.+?)((\.path(?<path>.+)))',
                                    'spec'    => '/.entity/%entity%/.path/%path%',
                                    'defaults' => array(
                                            'controller' => 'resource_front_controller',
                                            'path' => '',
                                            'source' => 'entity',
                                    ),
                            ),
                    ),

//                     'backend' => array(
//                         'type' => 'Zend\Mvc\Router\Http\Regex',
//                         'options' => array(
//                             'regex'    => '/system/manager/(?<path>.*)',
//                             'spec'    => '/system/manager/%path%',
//                             'defaults' => array(
//                                 'controller' => 'cms_front_controller',
//                                 'path' => '',
//                                 'module' => '',
//                             ),
//                         ),
//                     ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'allow_override' => true,
        'invokables'    => array(
            'uuid_generator'            => 'Vivo\Uuid\Generator',
            'storage_factory'           => 'Vivo\Storage\Factory',
            'site_event'                => 'Vivo\SiteManager\Event\SiteEvent',
            'io_util'                   => 'Vivo\IO\IOUtil',
            'indexer_query_builder'     => 'Vivo\Indexer\QueryBuilder',
            'indexer_document_builder'  => 'Vivo\Indexer\DocumentBuilder',
            'view_model'                => 'Zend\View\Model\ViewModel',
            'session_manager'           => 'Zend\Session\SessionManager',
        ),
        'factories' => array(
            'translator'                => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'response'                  => 'Vivo\Service\ResponseFactory',
            'db_service_manager'        => 'Vivo\Service\DbServiceManagerFactory',
            'uuid_convertor'            => 'Vivo\Service\UuidConvertorFactory',
            'module_storage'            => 'Vivo\Service\ModuleStorageFactory',
            'remote_module'             => 'Vivo\Service\RemoteModuleFactory',
            'module_storage_manager'    => 'Vivo\Service\ModuleStorageManagerFactory',
            'module_manager_factory'    => 'Vivo\Service\ModuleManagerFactoryFactory',
            'site_manager'              => 'Vivo\Service\SiteManagerFactory',
            'run_site_manager_listener' => 'Vivo\Service\RunSiteManagerListenerFactory',
            'lucene'                    => 'Vivo\Service\LuceneFactory',
            'storage_util'              => 'Vivo\Service\StorageUtilFactory',
            'indexer_adapter_lucene'    => 'Vivo\Service\IndexerAdapterLuceneFactory',
            'indexer'                   => 'Vivo\Service\IndexerFactory',
            'repository'                => 'Vivo\Service\RepositoryFactory',
            'indexer_helper'            => 'Vivo\Service\IndexerHelperFactory',
            'cms'                       => 'Vivo\Service\CmsFactory',
            'module_resource_manager'   => 'Vivo\Service\ModuleResourceManagerFactory',
            'module_install_manager'    => 'Vivo\Service\ModuleInstallManagerFactory',
            'cms_api_module'            => 'Vivo\Service\CmsApiModuleFactory',
            'cms_api_repository'        => 'Vivo\Service\CmsApiRepositoryFactory',
            'db_provider_factory'       => 'Vivo\Service\DbProviderFactoryFactory',
            'pdo_abstract_factory'      => 'Vivo\Service\PdoAbstractFactoryFactory',
            'zdb_abstract_factory'      => 'Vivo\Service\ZdbAbstractFactoryFactory',
            'path_builder'              => 'Vivo\Service\PathBuilderFactory',
            'component_factory'         => 'Vivo\Service\ComponentFactoryFactory',
            'phtml_rendering_strategy'  => 'Vivo\Service\PhtmlRenderingStrategyFactory',
            'solr_service'              => 'Vivo\Service\SolrServiceFactory',
            'indexer_adapter'           => 'Vivo\Service\IndexerAdapterFactory',
            'indexer_field_helper'      => 'Vivo\Service\IndexerFieldHelperFactory',
            'indexer_query_parser'      => 'Vivo\Service\IndexerQueryParserFactory',
            'module_name_resolver'      => 'Vivo\Service\ModuleNameResolverFactory',
            'metadata_manager'          => 'Vivo\Service\MetadataManagerFactory',
            'redirector'                => 'Vivo\Service\RedirectorFactory',
            'logger'                    => 'Vivo\Service\LoggerFactory',
            'default_log'               => 'Vivo\Service\LogFileWriterFactory',
            'template_resolver'         => 'Vivo\Service\TemplateResolverFactory',
            'di_proxy'                  => 'Vivo\Service\DiProxyFactory',
            'module_db_provider'        => 'Vivo\Service\ModuleDbProviderFactory',
        ),
        'aliases' => array(
            'Vivo\SiteManager\Event\SiteEvent'  => 'site_event',
            'Vivo\Repository\Repository'        => 'repository',
            'Zend\Http\Response'                => 'response',
            'Zend\Http\Request'                 => 'request',
            'Zend\View\HelperPluginManager'     => 'view_helper_manager',
            'Vivo\Util\Redirector'              => 'redirector',
            'Vivo\CMS\Api\CMS'                  => 'cms',
            'Zend\View\Model\ViewModel'         => 'view_model',
            'Zend\Session\SessionManager'       => 'session_manager',
        ),
        'shared' => array(
            'view_model' => false,
        ),
        'initializers' => array(
            'component' => 'Vivo\Service\Initializer\ComponentInitializer',
            'default'   => 'Vivo\Service\Initializer\DefaultInitializer',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'cli_info'      => 'Vivo\Controller\CLI\InfoController',
        ),
        'factories' => array(
            'cms_front_controller'      => 'Vivo\Service\Controller\CMSFrontControllerFactory',
            'resource_front_controller' => 'Vivo\Service\Controller\ResourceFrontControllerFactory',
            'cli_module'                => 'Vivo\Service\Controller\CLI\CLIModuleControllerFactory',
            'cli_repository'            => 'Vivo\Service\Controller\CLI\CLIRepositoryControllerFactory',
            'cli_cms'                   => 'Vivo\Service\Controller\CLI\CLICmsControllerFactory',
            'cli_indexer'               => 'Vivo\Service\Controller\CLI\CLIIndexerControllerFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'vivo/index/index' => __DIR__ . '/../view/vivo/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'action'            => 'Vivo\View\Helper\Action',
            'action_link'       => 'Vivo\View\Helper\ActionLink',
            'action_url'        => 'Vivo\View\Helper\ActionUrl',
            'vivoform'          => 'Vivo\View\Helper\VivoForm',
            'vivoformfieldset'  => 'Vivo\View\Helper\VivoFormFieldset',
        ),
    ),

    'di' => array(
    ),
    'metadata_manager' => array(
        'config_path' => __DIR__ . '/../config/metadata',
    ),

    'logger' => array(
        'listener' => array (
            'attach' => array (
                //array('*', 'log'), //log 'log' events
                //array('*', '*'), //log all events
            ),
        ),
        'writers' => array (
            //writers from writer plugin manager or main service manager
            'default_log',
            //'firephp',
        ),
    ),
    'db_service'    => array(
        'abstract_factory'  => array(
            //PDO
            'pdo'       => array(
                'service_identifier'    => 'pdo',
                //The PDO connections are defined in a local config
                /*
                'config'                => array(
                    'config_name'    => array(
                        'dsn'       => '',
                        'username'  => '',
                        'password'  => '',
                        'options'   => array(
                        ),
                    ),
                ),
                */
            ),
            //Doctrine
            'dem'  => array(
                'service_identifier'    => 'dem',
            ),
            //Zend DB Adapter
            'zdb'  => array(
                'service_identifier'    => 'zdb',
            ),
        ),
    ),
    'indexer'   => array(
        'adapter'   => array(
            'type'      => 'dummy',
            //Solr options
//                'options'   => array(
//                    'id_field'      => 'uuid',
//                    'solr_service'  => array(
//                        'host'          => 'localhost',
//                        'port'          => 8983,
//                        'path'          => '/solr/',
//                    ),
//                ),
        ),
        'default_indexing_options'  => array(
            'type'          => \Vivo\Indexer\IndexerInterface::FIELD_TYPE_STRING,
            'indexed'       => true,
            'stored'        => true,
            'tokenized'     => false,
            'multi'         => false,
        ),
        'presets'                   => array(
        ),
    ),
    'security_manager'  => array(
        //Options for Vivo\CMS\Security\Simple\Manager
        'options'           => array(
            //Security domain - if not set, the security domain of the active site will be used
//                'security_domain'   => 'my.security.domain',
            'username'          => 'vivo.user',
            'password'          => 'password',
        ),
    ),
    //Vivo Modules configuration
    'modules'  => array(
        //Name of stream (protocol) which will be registered for Vivo Module source file access in Storage
        'stream_name'   => 'vivo.module',
        //Vivo Module paths in Vivo Module Storage
        'module_paths'              => array(
            '/',
        ),
        'descriptor_name'       => 'vivo_module.json',
        //Default path where new modules will be added (in the module storage)
        'default_install_path'  => '/',
        //List of core modules loaded for all sites
        'core_modules'          => array(
        ),
        //Module resource manager configuration options
        'resource_manager'      => array(
            //Mapping of resource types to folders within modules
            'type_map'      => array(
                'view'      => 'view',
                'layout'    => 'view/layout',
                'resource'  => 'resource',
                'metadata'  => 'config/metadata',
            ),
            //Default resource type
            'default_type'  => 'resource',
        ),
        //Default db source used for modules
        //Configure in local config
        //'default_db_source'     => '',
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'info' => array(
                    'options' => array(
                        'route'    => 'info [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_info',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module' => array(
                    'options' => array(
                        'route'    => 'module [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'module_add' => array(
                    'options' => array(
                        'route'    => 'module add <module_url> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'add',
                        ),
                    ),
                ),
                'module_install' => array(
                    'options' => array(
                        'route'    => 'module install <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'install',
                        ),
                    ),
                ),
                'module_uninstall' => array(
                    'options' => array(
                        'route'    => 'module uninstall <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'uninstall',
                        ),
                    ),
                ),
                'module_enable' => array(
                    'options' => array(
                        'route'    => 'module enable <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'enable',
                        ),
                    ),
                ),
                'module_disable' => array(
                    'options' => array(
                        'route'    => 'module disable <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'disable',
                        ),
                    ),
                ),
                'module_is_installed' => array(
                    'options' => array(
                        'route'    => 'module isinstalled <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'isInstalled',
                        ),
                    ),
                ),
                'module_is_enabled' => array(
                    'options' => array(
                        'route'    => 'module isenabled <module_name> [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'isEnabled',
                        ),
                    ),
                ),
                'module_get_installed' => array(
                    'options' => array(
                        'route'    => 'module getinstalled [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'getInstalled',
                        ),
                    ),
                ),
                'module_get_enabled' => array(
                    'options' => array(
                        'route'    => 'module getenabled [<site>]',
                        'defaults' => array(
                            'controller' => 'cli_module',
                            'action'     => 'getEnabled',
                        ),
                    ),
                ),
                'indexer' => array(
                    'options' => array(
                        'route'    => 'indexer [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_indexer',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'indexer_clear' => array(
                    'options' => array(
                        'route'    => 'indexer clear',
                        'defaults' => array(
                            'controller' => 'cli_indexer',
                            'action'     => 'clear',
                        ),
                    ),
                ),
                'cms' => array(
                    'options' => array(
                        'route'    => 'cms [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'cms_reindex' => array(
                    'options' => array(
                        'route'    => 'cms reindex <host>',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'reindex',
                        ),
                    ),
                ),
                'repository' => array(
                    'options' => array(
                        'route'    => 'repository [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_repository',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'repository_host_action' => array(
                    'options' => array(
                        'route'    => 'repository <action> <host>',
                        'defaults' => array(
                            'controller' => 'cli_repository',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'repository_unique_uuids' => array(
                    'options' => array(
                        'route'    => 'repository uniqueuuids <host> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_repository',
                            'action'     => 'unique-uuids',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'cms'      => array(
        //this config key is reserved for merged cms configuration
        //default values and structure are in cms.config.php
    ),
);
