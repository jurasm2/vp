<?php
namespace Vivo\Controller\CLI;

use Vivo\Module\StorageManager\StorageManager as ModuleStorageManager;
use Vivo\Module\StorageManager\RemoteModule;
use Vivo\Module\InstallManager\Exception as InstallException;
use Vivo\CMS\Api\Module as ModuleApi;
use Vivo\Repository\Repository;

use Zend\Console\Request as ConsoleRequest;

/**
 * Vivo CLI controller for command 'module'
 */
class ModuleController extends AbstractCliController
{
    const COMMAND = 'module';

    /**
     * Module Storage Manager
     * @var ModuleStorageManager
     */
    protected $moduleStorageManager;

    /**
     * Remote Module
     * @var RemoteModule
     */
    protected $remoteModule;

    /**
     * Module API
     * @var ModuleApi
     */
    protected $moduleApi;

    /**
     * Repository
     * @var Repository
     */
    protected $repository;

    /**
     * Constructor
     * @param \Vivo\Module\StorageManager\StorageManager $moduleStorageManager
     * @param \Vivo\Module\StorageManager\RemoteModule $remoteModule
     * @param \Vivo\Repository\Repository $repository
     * @param \Vivo\CMS\Api\Module $moduleApi
     */
    public function __construct(ModuleStorageManager $moduleStorageManager,
                                RemoteModule $remoteModule,
                                Repository $repository,
                                ModuleApi $moduleApi)
    {
        $this->moduleStorageManager = $moduleStorageManager;
        $this->remoteModule         = $remoteModule;
        $this->repository           = $repository;
        $this->moduleApi            = $moduleApi;
    }

    public function defaultAction()
    {
        return $this->listAction();
    }

    public function getConsoleUsage()
    {
        return 'module usage: ...';
    }

    /**
     * Checks if a module is installed
     */
    public function isInstalledAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module isinstalled <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt query
        try {
            $is = $this->moduleApi->isInstalled($module, $site);
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during query');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        if (is_null($site)) {
            if ($is) {
                //Core module installed
                return sprintf("Core module '%s' is installed", $module);
            } else {
                //Core module not installed
                return sprintf("Core module '%s' is not installed", $module);
            }
        } else {
            if ($is) {
                //Site module installed
                return sprintf("Module '%s' is installed in site '%s'", $module, $site);
            } else {
                //Site module not installed
                return sprintf("Module '%s' is not installed in site '%s'", $module, $site);
            }
        }
    }

    /**
     * Checks if a module is enabled
     */
    public function isEnabledAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module isenabled <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt query
        try {
            $is = $this->moduleApi->isEnabled($module, $site);
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during query');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        if (is_null($site)) {
            if ($is) {
                //Core module enabled
                return sprintf("Core module '%s' is enabled", $module);
            } else {
                //Core module not enabled
                return sprintf("Core module '%s' is not enabled", $module);
            }
        } else {
            if ($is) {
                //Site module enabled
                return sprintf("Module '%s' is enabled in site '%s'", $module, $site);
            } else {
                //Site module not enabled
                return sprintf("Module '%s' is not enabled in site '%s'", $module, $site);
            }
        }
    }

    /**
     * Returns list of installed modules
     */
    public function getInstalledAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $site       = $request->getParam('site');
        //Attempt query
        try {
            $modules    = $this->moduleApi->getInstalledModules($site);
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during query');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        if (count($modules)) {
            if ($site) {
                $output = sprintf("Modules installed in site '%s':", $site);
            } else {
                $output = "Modules installed in core:";
            }
            foreach ($modules as $module) {
                $output .= "\n" . $module;
            }
        } else {
            if ($site) {
                $output = sprintf("No modules installed in site '%s'", $site);
            } else {
                $output = "No modules installed in core";
            }
        }
        return $output;
    }

    /**
     * Returns list of enabled modules
     */
    public function getEnabledAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $site       = $request->getParam('site');
        //Attempt query
        try {
            $modules    = $this->moduleApi->getEnabledModules($site);
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during query');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        if (count($modules)) {
            if ($site) {
                $output = sprintf("Modules enabled in site '%s':", $site);
            } else {
                $output = "Modules enabled in core:";
            }
            foreach ($modules as $module) {
                $output .= "\n" . $module;
            }
        } else {
            if ($site) {
                $output = sprintf("No modules enabled in site '%s'", $site);
            } else {
                $output = "No modules enabled in core";
            }
        }
        return $output;
    }

    /**
     * Installs a module into a site or globally (a core module)
     * @return string
     */
    public function installAction()
    {
        //TODO - implement support for custom db source specified in CLI
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module install <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt installation
        try {
            $this->moduleApi->install($module, $site);
            $this->repository->commit();
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (InstallException\ModuleAlreadyInstalledException $e) {
            //Module is already installed
            $output = sprintf("Module '%s' is already installed", $module);
            return $output;
        } catch (InstallException\ModuleNotFoundInStorageException $e) {
            //Module has not been found in storage
            $output = sprintf("Module '%s' not found in storage; add the module first", $module);
            return $output;
        } catch (InstallException\InstallCoreModuleToSiteException $e) {
            //Trying to install a core module to a site
            $output = sprintf("Cannot install the core module '%s' to a site", $module);
            return $output;
        } catch (InstallException\NoSiteSpecifiedException $e) {
            //Trying to install a site module without a site specification
            $output = sprintf("Site specification missing when installing site module '%s'", $module);
            return $output;
        } catch (\Vivo\Service\Exception\DbSourceDoesNotExistException $e) {
            //The specified db source does not exist
            $output = sprintf("The db source does not exist", $module);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during module installation');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        //Everything ok
        if (is_null($site)) {
            $output = sprintf("Core module '%s' has been installed", $module);
        } else {
            $output = sprintf("Module '%s' has been installed into site '%s'", $module, $site);
        }
        return $output;
    }

    /**
     * Uninstalls a module
     */
    public function uninstallAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module uninstall <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt uninstallation
        try {
            $this->moduleApi->uninstall($module, $site);
            $this->repository->commit();
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (InstallException\ModuleNotInstalledException $e) {
            //Module not installed
            $output = sprintf("Module '%s' not installed", $module);
            return $output;
        } catch (InstallException\ModuleEnabledException $e) {
            //Module enabled
            $output = sprintf("Cannot uninstall module '%s'; the Module is enabled", $module);
            return $output;
        } catch (\Vivo\Service\Exception\DbSourceDoesNotExistException $e) {
            //The db source specified in config does not exist
            $output = sprintf("The db source does not exist", $module);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during module uninstallation');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        //Everything ok
        if (is_null($site)) {
            $output = sprintf("Core module '%s' has been uninstalled", $module);
        } else {
            $output = sprintf("Module '%s' has been uninstalled from site '%s'", $module, $site);
        }
        return $output;
    }

    /**
     * Enables a module
     */
    public function enableAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module enable <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt enabling
        try {
            $this->moduleApi->enable($module, $site);
            $this->repository->commit();
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (InstallException\ModuleNotInstalledException $e) {
            //Module not installed
            $output = sprintf("Module '%s' not installed", $module);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during module enabling');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        //Everything ok
        if (is_null($site)) {
            $output = sprintf("Module '%s' has been enabled in core", $module);
        } else {
            $output = sprintf("Module '%s' has been enabled in site '%s'", $module, $site);
        }
        return $output;
    }

    /**
     * Disables a module
     */
    public function disableAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $module     = $request->getParam('module_name');
        if (is_null($module)) {
            return 'Usage: module disable <module_name> [<site>]';
        }
        $site       = $request->getParam('site');
        //Attempt disabling
        try {
            $this->moduleApi->disable($module, $site);
            $this->repository->commit();
        } catch (InstallException\SiteDoesNotExistException $e) {
            //Site does not exist
            $output = sprintf("Site '%s' does not exist", $site);
            return $output;
        } catch (InstallException\ModuleNotInstalledException $e) {
            //Module not installed
            $output = sprintf("Module '%s' not installed", $module);
            return $output;
        } catch (\Exception $e) {
            //Other exception
            $output = sprintf('An exception occurred during module disabling');
            $output .= "\n" . $e->getMessage();
            return $output;
        }
        //Everything ok
        if (is_null($site)) {
            $output = sprintf("Module '%s' has been disabled in core", $module);
        } else {
            $output = sprintf("Module '%s' has been disabled in site '%s'", $module, $site);
        }
        return $output;
    }

    /**
     * Removes a module from storage
     */
    public function removeAction()
    {
        //TODO - implement removeAction()
        throw new \Exception(sprintf('%s not implemented', __METHOD__));
    }

    /**
     * Lists the modules present in storage
     * @return string
     */
    public function listAction()
    {
        $rowTemplate    = "%-1.1s %-30.30s %-10.10s %-34.34s\n";
        $output         = "\nModules in storage\n\n";
        $output         .= sprintf($rowTemplate, 'C', 'Name', 'Version', 'Description');
        $output         .= str_repeat('-', 78) . "\n";
        $modulesInfo    = $this->moduleStorageManager->getModulesInfo();
        foreach ($modulesInfo as $moduleInfo) {
            $descriptor =  $moduleInfo['descriptor'];
            if (isset($descriptor['description'])) {
                $description    = $descriptor['description'];
            } else {
                $description    = '';
            }
            if ($descriptor['type'] == 'core') {
                $moduleType = 'C';
            } else {
                $moduleType = '';
            }
            $output     .= sprintf($rowTemplate, $moduleType, $moduleInfo['name'],
                                   $descriptor['version'], $description);
        }
        return $output;
    }

    /**
     * Adds a module to storage
     * @return string
     */
    public function addAction()
    {
        //$moduleUrl should be in format: 'file://<full path to the module dir>'
        $request    = $this->getRequest();
        /* @var $request ConsoleRequest */
        $moduleUrl  = $request->getParam('module_url');
        if (!$moduleUrl) {
            return 'Usage: module add <module_url> [--force|-f]';
        }
        $force      = $request->getParam('force', false) || $request->getParam('f', false);
        $moduleDescriptor   = $this->remoteModule->getModuleDescriptor($moduleUrl);
        //Check VModule dependencies
        if ((!$force) && (isset($moduleDescriptor['require']))) {
            $dependencies   = $moduleDescriptor['require'];
            $info           = array();
            if (!$this->moduleStorageManager->checkVmoduleDependencies($dependencies, $info)) {
                $output     = sprintf("\nSome dependencies of module '%s' are missing.\n", $moduleDescriptor['name']);
                $output     .= $this->formatDependencyTable($info);
                return $output;
            }
        }
        $moduleInfo = $this->moduleStorageManager->addModule($moduleUrl, $force);
        $output     = sprintf("\nModule '%s' has been added to storage.\n", $moduleInfo['name']);
        $output     .= $this->formatModuleInfo($moduleInfo);
        return $output;
    }

    /**
     * Returns a formatted string containing the dependency table
     * @param array $dependencyTable
     * @return string
     */
    protected function formatDependencyTable(array $dependencyTable)
    {
        $rowTemplate    = "%-30.30s %-14.14s %-14.14s %-5.5s\n";
        $output         = "\nDependency table\n\n";
        $output         .= sprintf($rowTemplate, 'Name', 'Required ver.', 'Current ver.', 'OK');
        $output         .= str_repeat('-', 66) . "\n";
        foreach ($dependencyTable as $row) {
            $output     .= sprintf($rowTemplate,
                            $row['name'],
                            $row['required_version'],
                            $row['present_version'],
                            $row['dependency_ok'] ? 'YES' : 'NO');
        }
        return $output;
    }

    /**
     * Returns a formatted string containing the module info
     * @param array $info
     * @return string
     */
    protected function formatModuleInfo(array $info)
    {
        $rowTemplate    = "%-20.20s %-40.40s\n";
        $output         = "\nModule info\n";
        $output         .= str_repeat('-', 61) . "\n";
        $output         .= sprintf($rowTemplate, 'Name:', $info['name']);
        $output         .= sprintf($rowTemplate, 'Version:', $info['descriptor']['version']);
        $output         .= sprintf($rowTemplate, 'Type:', $info['descriptor']['type']);
        $output         .= sprintf($rowTemplate, 'Storage path:', $info['storage_path']);
        return $output;
    }
}
