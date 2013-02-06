<?php
/**
 * CMS config (cms_config), this configuration could be overwritten
 * by configuration in modules and sites.
 * This config is registered as 'cms_config' service, when all configuration
 * sources (site, modules) are merged.
 */
return array(
    'templates' => array (
        'template_map' => array(
            'Vivo\UI\Page'                      => __DIR__.'/../view/Vivo/UI/Page.phtml',
            'Vivo\CMS\UI\Content\Layout'        => __DIR__.'/../view/Vivo/CMS/UI/Content/Layout.phtml',
            'Vivo\CMS\UI\Content\File:html'     => __DIR__.'/../view/Vivo/CMS/UI/Content/File.html.phtml',
            'Vivo\CMS\UI\Content\File:plain'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.plain.phtml',
            'Vivo\CMS\UI\Content\File:flash'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.flash.phtml',
            'Vivo\CMS\UI\Content\File:image'    => __DIR__.'/../view/Vivo/CMS/UI/Content/File.image.phtml',
            'Vivo\CMS\UI\Content\File'          => __DIR__.'/../view/Vivo/CMS/UI/Content/File.phtml',
            'Vivo\CMS\UI\Content\Overview'      => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.phtml',
            'Vivo\CMS\UI\Content\Overview:Description' => __DIR__.'/../view/Vivo/CMS/UI/Content/Overview.Description.phtml',
            'Vivo\CMS\UI\Content\Logon'         => __DIR__.'/../view/Vivo/CMS/UI/Content/Logon.phtml',
            'Vivo\UI\ComponentContainer'        => __DIR__.'/../view/Vivo/UI/ComponentContainer.phtml',
            'Vivo\UI\TabContainer'              => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon'                    => __DIR__.'/../view/Vivo/UI/TabContainer.phtml',
            'Vivo\UI\Ribbon\Tab'                => __DIR__.'/../view/Vivo/UI/Ribbon/Tab.phtml',
            'Vivo\UI\Ribbon\Group'              => __DIR__.'/../view/Vivo/UI/Ribbon/Group.phtml',
            'Vivo\UI\Ribbon\Item'               => __DIR__.'/../view/Vivo/UI/Ribbon/Item.phtml',
        ),
        'custom_templates' => array (
            // custom selectable templates
            'Vivo\CMS\Model\Content\Overview' => array (
                'Vivo\CMS\UI\Content\Overview:Description',
                'Vivo\CMS\UI\Content\Overview',
            ),
        ),
    ),
    'component_mapping' => array (
        'front_component' => array (
            'Vivo\CMS\Model\Content\Layout'     => 'Vivo\CMS\UI\Content\Layout',
            'Vivo\CMS\Model\Content\File'       => 'Vivo\CMS\UI\Content\File',
            'Vivo\CMS\Model\Content\Overview'   => 'Vivo\CMS\UI\Content\Overview',
            'Vivo\CMS\Model\Content\Hyperlink'  => 'Vivo\CMS\UI\Content\Hyperlink',
            'Vivo\CMS\Model\Content\Logon'      => 'Vivo\CMS\UI\Content\Logon',
        ),
        'editor_component' => array (

        ),
    ),
    'service_manager' => array (
        //configuration of service manager, services defined here should not override
        //services defined in Vivo config
        'invokables' => array (
            'Vivo\CMS\UI\Root'              => 'Vivo\CMS\UI\Root',
            'Vivo\UI\ComponentContainer'    => 'Vivo\UI\ComponentContainer',
            'Vivo\UI\TabContainer'          => 'Vivo\UI\TabContainer',
            'layout_empty_panel'            => 'Vivo\UI\Text',
        ),
        'factories' => array (
            'Vivo\CMS\UI\Content\File'      => 'Vivo\CMS\Service\UI\Content\FileFactory',
            'Vivo\CMS\UI\Content\Hyperlink' => 'Vivo\CMS\Service\UI\Content\HyperlinkFactory',
            'Vivo\CMS\UI\Content\Layout'    => 'Vivo\CMS\Service\UI\Content\LayoutFactory',
            'Vivo\CMS\UI\Content\Overview'  => 'Vivo\CMS\Service\UI\Content\OverviewFactory',
            'Vivo\CMS\UI\Content\Logon'     => 'Vivo\CMS\Service\UI\Content\LogonFactory',
            'Vivo\UI\Page'                  => 'Vivo\Service\UI\PageFactory',

            'security_manager'              => 'Vivo\Service\SimpleSecurityManagerFactory',
//            'security_manager'              => 'Vivo\Service\DbSecurityManagerFactory',
        ),
        'aliases' => array(
        ),
        'shared' => array(
        ),
        'initializers' => array(
        ),
    ),
    'di' => array (
        'instance' => array (
            'alias' => array (
            ),
            'Vivo\UI\Component' => array (
                'injection' => array (
                ),
                'parameters' => array (
                    'view' => 'Zend\View\Model\ViewModel',
                ),
            ),
        ),
    ),

    'ui' => array (
        //configuration of ui components
        'Vivo\UI\Page' => array (
            'doctype' => 'HTML5',
//                 'links' => array (
//                     array(
//                         'rel'  => 'stylesheet',
//                         'href' => '/.ModuleName.resource/css/definedInVivoConfig.css',
//                         'type' => 'text/css',
//                         'media' => 'screen'
//                     ),
//                 ),
//                 'scripts' => array (
//                     array(
//                         'src' => '/.ModuleName.resource/js/front.js',
//                         'type' => 'text/javascript',
//                     ),
//                 ),
            'metas' => array (
//                     array (
//                         'name' => 'Robots',
//                         'content' => 'INDEX,FOLLOW',
//                     ),
//                     array (
//                         'charset' => 'UTF-8',
//                     ),
                array (
                    'http-equiv' => 'Content-Type',
                    'content' => 'text/html',
                    'charset' => 'utf-8',
                ),
            ),
        ),
    ),
    'output_filters' => array (
        //register output filters
        //'Vivo\Http\Filter\UpperCase',
    )
);
