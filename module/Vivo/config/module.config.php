<?php
/**
 * Main Vivo config, this config can not be overwritten by sites, and modules.
 */
return array(
    'router' => array(
        'routes' => array(
            //routes for frontend
            'vivo' => array(
                //only add hostname to routermatch
                'type' => 'Vivo\Router\Hostname',
                'may_terminate' => false,
                'child_routes' => array(
                    //route for frontend documents
                    //@example http://<sitehost>/<path>/
                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '(?<path>/.*)',
                            'spec'    => '%path%',
                            'defaults' => array(
                                'controller' => 'cms_front_controller',
                                'path' => '',
                            ),
                        ),
                        'may_terminate' => false,
                        'child_routes' => array(
                            'query' => array(
                                'type' => 'Query',
                            ),
                        ),
                    ),
                    //route for fronend resources
                    //@example http://<sitehost>/.<moduleName>.<resourceType>/<path>
                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),
                    //route for entity resources
                    //@example http://<sitehost>/.entity/<entityPath>/.path/<resourcePath>
                    'resource_entity' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.entity(?<entity>.+?)((\.path/(?<path>.+)))',
                            'spec'    => '/.entity%entity%.path/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'path' => '',
                                'source' => 'entity',
                            ),
                        ),
                    ),
                ),
            ),

            //routes configuration for backend
            'backend' => array(
                'type' => 'Vivo\Backend\Hostname',
                'may_terminate' => false,
                'options' => array (
                    'hosts' => array (
                    ),
                ),
                'child_routes' => array(
                    //route for everithing else on backend hostname - controller redirects
                    'other' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => true,
                        'options' => array(
                            'regex'    => '.*',
                            'spec'    => '/',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                            ),
                        ),
                    ),
                    //default backend route
                    //@example http://<backendhost>/
                    'default' => array (
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                            ),
                        ),
                    ),
                    //route for backend modules
                    //@example http://<backendhost>/<sitehost>/<moduleName>/
                    'modules' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => false,
                        'options' => array(
                            'regex'    => '/(?<host>.+?)/(?<module>.+?)/(?<path>.*)',
                            'spec'    => '/%host%/%module%/%path%',
                            'defaults' => array(
                                'controller' => 'backend_controller',
                                'path'   => '',
                                'module' => 'explorer',
                                'host' => '',
                            ),
                        ),
                        'child_routes' => array(
                                'query' => array(
                                        'type' => 'Query',
                                ),
                        ),
                    ),
                    //route for viewing site in backend
                    //@example http://<backendhost>/<sitehost>/view/<pathWithinSite>
                    'cms' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'may_terminate' => false,
                        'options' => array(
                            'regex'    => '/(?<host>.*)/view(?<path>/.*)?',
                            'spec'    => '/%host%/view%path%',
                            'defaults' => array(
                                'controller' => 'cms_front_controller',
                                'path'   => '',
                            ),
                        ),
                        'child_routes' => array(
                            'query' => array(
                                'type' => 'Query',
                            ),
                        ),
                    ),
                    //route for site resources in backend view
                    //@example http://<backendhost>/<sitehost>/view/.<moduleName>.<resourceType>/<path>
                    'resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/(?<host>.+)/view/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/%host%/view/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),
                    //route for site entity resources in backend view
                    'resource_entity' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/(?<host>.+)/view/\.entity(?<entity>.+?)((\.path/(?<path>.+)))',
                            'spec'    => '/%host%/view/.entity%entity%.path/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'path' => '',
                                'source' => 'entity',
                            ),
                        ),
                    ),

                    //route for resources for backend
                    'backend_resource' => array(
                        'type' => 'Zend\Mvc\Router\Http\Regex',
                        'options' => array(
                            'regex'    => '/\.(?<source>.+?)\.(?<type>.+?)/(?<path>.+)',
                            'spec'    => '/.%source%.%type%/%path%',
                            'defaults' => array(
                                'controller' => 'resource_front_controller',
                                'type' => '',
                                'path' => '',
                                'source' => '',
                            ),
                        ),
                    ),
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
            'Vivo\Http\Filter\OutputFilterListener' => 'Vivo\Http\Filter\OutputFilterListener',
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
            'repository_events'         => 'Vivo\Repository\EventManagerFactory',
            'indexer_helper'            => 'Vivo\Service\IndexerHelperFactory',
            'Vivo\CMS\Api\CMS'          => 'Vivo\Service\CmsFactory',
            'Vivo\CMS\Api\Module'       => 'Vivo\Service\CmsApiModuleFactory',
            'Vivo\CMS\Api\Document'     => 'Vivo\CMS\Api\DocumentFactory',
            'Vivo\CMS\Api\Indexer'      => 'Vivo\CMS\Api\IndexerFactory',
            'Vivo\CMS\Api\Site'         => 'Vivo\CMS\Api\SiteFactory',
            'module_resource_manager'   => 'Vivo\Service\ModuleResourceManagerFactory',
            'module_install_manager'    => 'Vivo\Service\ModuleInstallManagerFactory',
            'db_provider_factory'       => 'Vivo\Service\DbProviderFactoryFactory',
            'db_provider_core'          => 'Vivo\Service\DbProviderCoreFactory',
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
            'lookup_data_manager'       => 'Vivo\LookupData\LookupDataManagerFactory',
            'redirector'                => 'Vivo\Util\RedirectorFactory',
            'logger'                    => 'Vivo\Service\LoggerFactory',
            'default_log'               => 'Vivo\Service\LogFileWriterFactory',
            'template_resolver'         => 'Vivo\Service\TemplateResolverFactory',
            'di_proxy'                  => 'Vivo\Service\DiProxyFactory',
            'module_db_provider'        => 'Vivo\Service\ModuleDbProviderFactory',
            'db_table_name_provider'    => 'Vivo\Service\DbTableNameProviderFactory',
            'db_table_gateway_provider' => 'Vivo\Service\DbTableGatewayProviderFactory',
            'Vivo\CMS\Api\Manager\Manager' => 'Vivo\CMS\Api\Manager\ManagerFactory',
            'component_tree_controller' => 'Vivo\UI\ComponentTreeControllerFactory',
            'Vivo\CMS\AvailableContentsProvider' => 'Vivo\CMS\AvailableContentsProviderFactory',
            'Vivo\Metadata\Provider\SelectableTemplatesProvider' => 'Vivo\Metadata\Provider\SelectableTemplatesProviderFactory',
            'Vivo\Util\UrlHelper'       =>  'Vivo\Util\UrlHelperFactory',
            'Vivo\Http\HeaderHelper'    => 'Vivo\Http\HeaderHelperFactory',
            'Vivo\Transliterator\Path'  => 'Vivo\Transliterator\PathFactory',
        ),
        'aliases' => array(
            'Vivo\SiteManager\Event\SiteEvent'  => 'site_event',
            'Vivo\Repository\Repository'        => 'repository',
            'Zend\Http\Response'                => 'response',
            'Zend\Http\Request'                 => 'request',
            'Zend\View\HelperPluginManager'     => 'view_helper_manager',
            'Vivo\Util\Redirector'              => 'redirector',
            'Zend\View\Model\ViewModel'         => 'view_model',
            'Zend\Session\SessionManager'       => 'session_manager',
        ),
        'shared' => array(
            'view_model' => false,
            'component_tree_controller' => false,
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
            'cms_front_controller'      => 'Vivo\CMS\FrontControllerFactory',
            'resource_front_controller' => 'Vivo\Controller\ResourceFrontControllerFactory',
            'cli_module'                => 'Vivo\Service\Controller\CLI\CLIModuleControllerFactory',
            'cli_repository'            => 'Vivo\Service\Controller\CLI\CLIRepositoryControllerFactory',
            'cli_cms'                   => 'Vivo\Service\Controller\CLI\CLICmsControllerFactory',
            'cli_indexer'               => 'Vivo\Service\Controller\CLI\CLIIndexerControllerFactory',
            'cli_setup'                 => 'Vivo\Service\Controller\CLI\CLISetupControllerFactory',
            'backend_controller'         => 'Vivo\Backend\BackendControllerFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => false,
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
            'tiny_mce'          => 'Vivo\View\Helper\TinyMce',
          //  'url' => 'Vivo\View\Helper\Url',
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

    'response' => array (
        'headers' => array (
            'mime_type_expiration' => array (
                //define specific expiration time for content type
                'image/*' => 86400,
                'audio/*' => 86400,
                'text/*' => 86400,
                'font/*' => 86400,
                'application/x-shockwave-flash' => 86400,
                '*/*' => 86400, // other mime types
            ),
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
    //Core setup
    'setup'         => array(
        'db'    => array(
            //Mapping of symbolic core table names to real names used in db
            'table_names'   => array(
                'vivo_users'     => 'vivo_users',
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
                'indexer_host_action' => array(
                    'options' => array(
                        'route'    => 'indexer <action> <host>',
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
                'cms_host_action' => array(
                    'options' => array(
                        'route'    => 'cms <action> <host>',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'cms_unique_uuids' => array(
                    'options' => array(
                        'route'    => 'cms uniqueuuids <host> [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_cms',
                            'action'     => 'unique-uuids',
                        ),
                    ),
                ),
                'setup' => array(
                    'options' => array(
                        'route'    => 'setup [<action>]',
                        'defaults' => array(
                            'controller' => 'cli_setup',
                            'action'     => 'default',
                        ),
                    ),
                ),
                'setup_db' => array(
                    'options' => array(
                        'route'    => 'setup db [--force|-f]',
                        'defaults' => array(
                            'controller' => 'cli_setup',
                            'action'     => 'db',
                        ),
                    ),
                ),

            ),
        ),
    ),
    'cms'      => array(
        //this config key is reserved for merged cms configuration
        //default values and structure are in cms.config.php
        //do not access to this key directly - use service 'cms_config'
    ),
);
