<?php
namespace Vivo\Module\InstallManager;

use Vivo\CMS\Model\Site;

/**
 * InstallManagerInterface
 */
interface InstallManagerInterface
{
    /**
     * Checks if a module is installed in a site | globally (a core module)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null, checks if the module is installed globally
     * @return bool
     */
    public function isInstalled($module, $site = null);

    /**
     * Checks if a module is enabled in a site | globally (a core module)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null, checks if the module is enabled globally
     * @return bool
     */
    public function isEnabled($module, $site = null);

    /**
     * Returns a list of modules installed for a site or globally (core modules)
     * @param Site|string|null $site Null = core modules
     * @return array
     */
    public function getInstalledModules($site = null);

    /**
     * Returns a list of modules enabled for a site or globally (core modules)
     * @param Site|string|null $site Null = core modules
     * @return array
     */
    public function getEnabledModules($site = null);

    /**
     * Installs module into a site or globally (core modules)
     * @param string $module Module name (ie module namespace)
     * @param Site|string|null $site If null installs into global scope (core modules)
     * @param array $config Module configuration
     */
    public function install($module, $site = null, array $config = array());

    /**
     * Uninstalls a module from a site or from global scope (core modules)
     * @param string $module
     * @param Site|string|null $site If null uninstalls from the global scope (core modules)
     */
    public function uninstall($module, $site = null);

    /**
     * Enables module for a site or globally (core modules)
     * @param string $module
     * @param Site|string|null $site
     */
    public function enable($module, $site = null);

    /**
     * Disables module for a site or globally (core modules)
     * @param string $module
     * @param Site|string|null $site
     */
    public function disable($module, $site = null);
}