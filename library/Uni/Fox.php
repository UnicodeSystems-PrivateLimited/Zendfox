<?php

/**
 * Zendfox Framework
 * 
 * LICENSE
 * 
 * This file is part of Zendfox.
 *
 * Zendfox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zendfox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zendfox in the file LICENSE.txt.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
/**
 * @see Uni_Application
 */
require_once 'Uni/Application.php';

/**
 * @see application_Fox
 */
require_once 'application/Fox.php';
//require_once 'Zend/Translate.php';

/**
 * Class Uni_Fox
 * provides functions for bootstrapping
 *  
 * @category    Uni 
 * @package     Uni
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 */
class Uni_Fox {

    /**
     * Module list
     * 
     * @static array
     */
    protected static $modules;

    /**
     * Db Adapter
     * 
     * @static Zend_Db_Adapter
     */
    protected static $dbAdapter;

    /**
     * Cache
     * 
     * @static Zend_Cache
     */
    protected static $cache;

    /**
     *
     * @static Zend_Translate 
     */
    protected static $translator;

    /**
     * Running mode normal
     * @static boolean 
     */
    protected static $modeRun = TRUE;

    /**
     * Application startup method
     * 
     * @return void
     */
    public static function runApp() {
        try {
            $application = new Uni_Application(
                    APPLICATION_ENV, APPLICATION_PATH . '/configs/system.xml'
            );
            $bs = $application->bootstrap();
            $bs->run();
            if (!Uni_Core_Installer::isInstalled()) {
                throw new Exception('Application is not installed.', 9999);
            }
        } catch (Exception $e) {
            $systemConfigPath = CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml';
            if ($e->getCode() == 9999 || !(file_exists($systemConfigPath) && is_readable($systemConfigPath))) {
                $urlParts = explode('/', $_SERVER['REQUEST_URI']);
                $urlPattern = '';
                if (count($urlParts) > 2) {
                    for ($i = 0; $i < count($urlParts); $i++) {
                        if ($urlParts[$i] == 'installer') {
                            $urlPattern = $urlParts[$i];
                            break;
                        }
                    }
                }
                if (!(strtolower($urlPattern) == 'installer')) {
                    self::runInstaller();
                } else {
                    self::$modeRun = FALSE;
                    Fox::setMode('installer');
                    $application = new Uni_Application(
                            APPLICATION_ENV, APPLICATION_PATH . '/configs/system_default.xml'
                    );
                    try {
                        $application->bootstrap()
                                ->run();
                    } catch (Exception $e) {
                        if (Uni_Core_Installer::isInstalled()) {
                            echo $e->getMessage() . '<br/>';
                            echo $e->getTraceAsString();
                        }
                    }
                }
            } else {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Starts installer
     *
     * @return void
     */
    public static function runInstaller() {
        if (!headers_sent()) {
            $reqUrl = $_SERVER['REQUEST_URI'];
            $splitUrl = explode('/', $reqUrl);
            $baseUrl = '';
            for ($i = 1; $i < count($splitUrl); $i++) {
                if ($splitUrl[$i]) {
                    $baseUrl = $baseUrl . $splitUrl[$i] . '/';
                }
            }
            header("Location: /" . $baseUrl . 'installer');
            return;
        }
    }

    /**
     * Initialize paths
     * 
     * @param string $basePath
     * @return string
     */
    public static function initPaths($basePath) {
        $loader = Zend_Loader_Autoloader::getInstance();
        $autoloader = new Zend_Application_Module_Autoloader(
                array(
            'namespace' => '',
            'basePath' => $basePath
                )
        );
        $loader->registerNamespace('Uni_');

        /**
         * Plugin Loader cache management.
         */
        if (Fox::getMode() != Uni_Controller_Action::MODE_INSTALL) {
            $classFileIncCache = APPLICATION_PATH . '/../var/data/pluginLoaderCache.php';
            if (file_exists($classFileIncCache)) {
                include_once $classFileIncCache;
                Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
            }
        }
    }

    /**
     * Bootstraps modules
     *
     * @param Uni_Application_Bootstrap $bootS
     * @return void
     */
    public static function initModules($bootS) {
        self::initCache();
        $bootS->bootstrap('frontController');
        $front = $bootS->frontController;
        $front->setDefaultModule('Cms');
        self::initDatabase($bootS);
        self::loadNamespacesAndModules($front, $bootS);
        $front->setRequest('Uni_Controller_Request_Http');
        $bootS->frontController->setParam('noViewRenderer', TRUE);
        $router = $bootS->frontController->getRouter();
        $dispatcher = $bootS->frontController->getDispatcher();
        $request = $bootS->frontController->getRequest();
        $defaultRoute = new Uni_Controller_Router_Route_Module(array(), $dispatcher, $request);
        $router->addRoute('default', $defaultRoute);
        self::initLogger();
        self::enableProfiler();
    }

    /**
     * Bootstraps session
     *
     * @param Uni_Application_Bootstrap $bootS
     * @return void
     */
    public static function initSession($bootS) {
        if (Fox::getMode() != Uni_Controller_Action::MODE_INSTALL) {
            session_set_cookie_params(0);
            if (!Fox::isDatabaseSession()) {
                Zend_Session::start(array('save_path' => APPLICATION_PATH . '/../var/sessions'));
            } else {
                $config = array(
                    'name' => Fox::getTableName('core_session'),
                    'primary' => 'id',
                    'modifiedColumn' => 'modified',
                    'dataColumn' => 'data',
                    'lifetimeColumn' => 'lifetime'
                );
                Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
                Zend_Session::start();
            }
        }
    }

    /**
     * Bootstraps caching system
     * 
     * @return void
     */
    public static function initCache() {
        self::$cache = self::getCache();
        Zend_Registry::set('Zend_Cache', self::$cache);
    }

    /**
     * Get Cache Object
     * 
     * @return Zend_Cache
     */
    public static function getCache() {
        $frontOpt = array(
            'automatic_serialization' => true
        );
        $backOpt = array(
            'cache_dir' => APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
        );
        return Zend_Cache::factory('Core', 'File', $frontOpt, $backOpt);
    }

    /**
     * Bootstraps locale resources
     *
     * @param Uni_Application_Bootstrap $bootS
     * @return void
     */
    public static function initLocale($bootS) {
        try {
            $bootS->bootstrap('locale');
            Zend_Registry::set('Zend_Locale', $bootS->locale);
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Logger Configuration
     * 
     * @return void
     */
    private static function initLogger() {
        $logger = new Zend_Log();
        $logFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log';
        if (!file_exists($logFile)) {
            if (!mkdir($logFile, 0777, TRUE)) {
                throw new Exception('Error creating log file');
            }
        }
        $writer = new Zend_Log_Writer_Stream($logFile . DIRECTORY_SEPARATOR . 'fox_log.txt');
        $logger->addWriter($writer);
        Zend_Registry::set('Zend_Log', $logger);
    }

    /**
     * Bootstraps database resources
     *
     * @param Uni_Application_Bootstrap $bootS
     * @return void
     */
    private static function initDatabase($bootS) {
        $bootS->bootstrap('db');
        self::$dbAdapter = $bootS->db;

        Zend_Db_Table_Abstract::setDefaultAdapter(self::$dbAdapter);
    }

    /**
     * Enabling profiler
     * 
     * @return void 
     */
    public static function enableProfiler() {
        // Enabling Profiler for firebug        
        if (Fox::isProfilerEnabled()) {
            $profiler = new Zend_Db_Profiler_Firebug('Zendfox DB System Queries');
            $profiler->setEnabled(TRUE);
            // Attach the profiler to your db adapter
            self::$dbAdapter->setProfiler($profiler);
        }
    }

    /**
     * Set database adapter
     * 
     * @param Zend_Db_Adapter $dbAdapter
     * @return Zend_Db_Adapter
     */
    public static function setDatabaseAdapter($dbAdapter) {
        return self::$dbAdapter = $dbAdapter;
    }

    /**
     * Get database adapater
     * 
     * @return Zend_Db_Adapter
     */
    public static function getDatabaseAdapter() {
        return self::$dbAdapter;
    }

    /**
     * Loads and initializes zendfox module system
     *
     * @param Zend_Controller_Front $front
     * @return array 
     */
    private static function loadNamespacesAndModules(Zend_Controller_Front $front, $bootS) {
        if (null == self::$modules) {
            self::$modules = array();
            $loader = Zend_Loader_Autoloader::getInstance();
            if ((self::$modules = self::$cache->load('fox_modules')) === false) {
                $dbConf = $bootS->db->getConfig();
                if (self::$modeRun) {
                    $mData = self::$dbAdapter->fetchAll("SELECT * FROM {$dbConf['prefix']}core_module");
                    foreach ($mData as $mInfo) {
                        $name = trim($mInfo['name']);
                        $enabled = trim($mInfo['status']);
                        $package = trim($mInfo['package']);
                        $nameSpc = trim($mInfo['namespace']);
                        if ($name != '' && $enabled == 1 && $package != '' && $nameSpc != '') {
                            $moduleDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $nameSpc . DIRECTORY_SEPARATOR . $name;
                            self::$modules[$name]['path'] = $moduleDir;
                            self::$modules[$name]['namespace'] = $nameSpc;
                        }
                    }
                    //Caching module info
                    self::$cache->save(self::$modules, 'fox_modules');
                } else {
//      ********Reading Module info from XML files**********************/
                    $doc = new DOMDocument();
                    if (is_dir(MODULE_CONFIG_DIR)) {
                        $dp = opendir(MODULE_CONFIG_DIR);
                        while (($file = readdir($dp))) {
                            if (!($file == '.' || $file == '..')) {
                                $fPath = MODULE_CONFIG_DIR . DIRECTORY_SEPARATOR . $file;
                                if (is_readable($fPath) && is_file($fPath)) {
                                    $doc->load($fPath);
                                    if ($doc->documentElement->childNodes) {
                                        $nList = $doc->documentElement->childNodes;
                                        foreach ($nList as $n) {
                                            if ($n->nodeName == 'module' && ($name = $n->getAttribute('name')) && ($enabled = ($n->getAttribute('enabled') === 'true')) && ($package = $n->getAttribute('package')) && ($nameSpc = $n->getAttribute('namespace'))) {
                                                if ($enabled) {
                                                    $moduleDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $nameSpc . DIRECTORY_SEPARATOR . $name;
                                                    self::$modules[$name]['path'] = $moduleDir;
                                                    self::$modules[$name]['namespace'] = $nameSpc;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*                     * ****End Reading module info from XML files************** */
                }
            }
            $ns = array();
            foreach (self::$modules as $name => $module) {
                $moduleDir = $module['path'];
                $nameSpc = $module['namespace'];
                if (!in_array($nameSpc, $ns)) {
                    $loader->registerNamespace($nameSpc . '_');
                    $ns[] = $nameSpc;
                }
                $front->addControllerDirectory($moduleDir . DIRECTORY_SEPARATOR . 'controllers', $name);
                //Translation loading
                if (NULL == self::$translator) {
                    self::$translator = new Zend_Translate(array(
                        'adapter' => 'csv',
                        'locale' => 'auto'
                    ));
                    Zend_Registry::set('translator', self::$translator);
//                                                self::$translator->setLocale(Zend_Registry::get('Zend_Locale'));
                }
                $langDir = $moduleDir . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
                if (file_exists($langDir)) {
                    self::$translator->addTranslation(array('content' => $langDir, 'scan' => Zend_Translate::LOCALE_FILENAME));
                }
            }
            unset($ns);
        }
    }

    /**
     * Load module config file
     * 
     * @param string $module
     * @return DOMDocument
     */
    public static function loadModuleConfig($module) {
        $doc = FALSE;
        if (isset(self::$modules[$module])) {
            $modulePath = self::$modules[$module]['path'] . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'config.xml';
            if (file_exists($modulePath)) {
                $doc = new DOMDocument();
                $doc->load($modulePath);
            }
        }
        return $doc;
    }

    /**
     * Load module setting file
     * 
     * @param string $module
     * @return DOMDocument
     */
    public static function loadModuleSetting($module) {
        $doc = FALSE;
        if (isset(self::$modules[$module])) {
            $modulePath = self::$modules[$module]['path'] . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'setting.xml';
            if (file_exists($modulePath)) {
                $doc = new DOMDocument();
                $doc->load($modulePath);
            }
        }
        return $doc;
    }

    /**
     * Get module list
     * 
     * @return array
     */
    public static function getModules() {
        return self::$modules;
    }

    /**
     * Retrieve wheteher system.xml file exits
     * 
     * @return boolean
     */
    public static function isSystemConfigExists() {
        return file_exists(CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml');
    }

}
