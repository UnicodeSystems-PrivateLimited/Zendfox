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
 * @see Uni_Core_Bean
 */
require_once 'Uni/Core/Bean.php';

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', 'uploads');
}
if (!defined('EXTENSION_DIR')) {
    define('EXTENSION_DIR', 'extensions');
}
if (!defined('CONFIG_DIR')) {
    define('CONFIG_DIR', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs');
}
if (!defined('VAR_DIR')) {
    define('VAR_DIR', 'var');
}
if (!defined('SESSION_DIR')) {
    define('SESSION_DIR', VAR_DIR . DIRECTORY_SEPARATOR . 'sessions');
}
if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', VAR_DIR . DIRECTORY_SEPARATOR . 'cache');
}
if (!defined('MODULE_CONFIG_DIR')) {
    define('MODULE_CONFIG_DIR', CONFIG_DIR . DIRECTORY_SEPARATOR . 'modules');
}
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

/**
 * Handles php version incompatibility for function lcfirst
 */
if (!function_exists('lcfirst')) {

    /**
     * Converts first character in lower case
     *
     * @param string $str String to convert first character in lower case
     * @return string Converted string 
     */
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return (string) $str;
    }

}

/**
 * Class Fox
 * general purpose class containing most used methods
 * 
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in> 
 */
final class Fox {
    /*     * #@+
     * resource type constants
     */

    const IF_MODEL = 'Model';
    const IF_HELPER = 'Helper';
    const IF_VIEW = 'View';
    const IF_FORM = 'Form';
    /*     * #@- */

    /*     * #@+
     * Collection type constants
     */
    const COLLECTION_MINIMAL = 1;
    const COLLECTION_COMMON = 2;
    const COLLECTION_FULL = 3;

    /*     * #@- */

    /**
     * Application mode
     *
     * @var string
     */
    private static $mode;

    /**
     * Registry collection
     * 
     * @var array
     */
    private static $registry = array();

    /**
     * Module path heirarchy
     *
     * @var array
     */
    private static $paths;

    /**
     * System preferences
     *
     * @var array
     */
    protected static $preferences = array();

    /**
     * Zend Http Request
     *
     * @var Zend_Controller_Request_Http 
     */
    protected static $_request;

    /**
     * Admin path
     *
     * @var string 
     */
    protected static $defaultAdminKey = 'admin';
    /**
     * Admin session timeout interval in seconds
     *
     * @var int 
     */
    protected static $defaultAdminSessionInterval = 1800;
    /**
     * Member session timeout interval in seconds
     *
     * @var int 
     */
    protected static $defaultMemberSessionInterval = 1800;
    /**
     * Database table prefix
     *
     * @var string 
     */
    private static $tablePrefix = FALSE;

    /**
     * Retrieve current Fox version string
     *
     * @return string
     */
    private static $translator;

    /**
     * Logger object
     *
     * @var Zend_Log 
     */
    private static $logger;

    public static function getVersion() {
        $version = self::getVersionInfo();
        return trim("{$version['major']}.{$version['minor']}.{$version['revision']}.{$version['patch']}");
    }

    /**
     * Retrieve detailed Fox version information
     *
     * @return array
     */
    public static function getVersionInfo() {
        return array(
            'major' => '1',
            'minor' => '0',
            'revision' => '0',
            'patch' => '0'
        );
    }

    /**
     * Retrieve include paths
     * 
     * @return array
     */
    public static function getPath() {
        if (!self::$paths) {
            self::$paths = array();
            array_push(self::$paths, APPLICATION_PATH . DS . 'modules' . DS . 'core');
            array_push(self::$paths, APPLICATION_PATH . DS . 'modules' . DS . 'ext');
            set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$paths));
        }
        return self::$paths;
    }

    /**
     * Creates instance of Model
     * 
     * @param string $model Takes model path
     * @param boolean $dup False innstantiates new object | TRUE retrieves already created
     * 
     * @return mixed Object of requested class or FALSE if not found 
     */
    public static function getModel($model, $dup = FALSE) {
        if (!$dup) {
            $mKey = 'models/' . $model;
            if (!array_key_exists($mKey, self::$registry) || !self::$registry[$mKey]) {
                self::$registry[$mKey] = Uni_Core_Bean::instantiate(self::getPath(), $model, self::IF_MODEL, 1);
            }
            return self::$registry[$mKey];
        }
        return Uni_Core_Bean::instantiate(self::getPath(), $model, self::IF_MODEL, 1);
    }

    /**
     * Gets appropriate view object specified by "$view" parameter
     * 
     * @param string $view View path or full qualified class name
     * @param string $key Unique identifier key
     * @param array $options
     * @param boolean $dup If true always creates new object else retrives existing one if it is.
     * @return Uni_Core_View 
     */
    public static function getView($view, $key = '', $options = array(), $dup = TRUE) {
        if (!$dup) {
            $vKey = 'views/' . $view;
            if (!array_key_exists($vKey, self::$registry) || !self::$registry[$vKey]) {
                self::$registry[$vKey] = Uni_Core_Bean::instantiate(self::getPath(), $view, self::IF_VIEW, 1);
            }
            $view = self::$registry[$vKey];
        } else {
            $view = Uni_Core_Bean::instantiate(self::getPath(), $view, self::IF_VIEW, 1);
        }
        if (!$view) {
            throw new Exception('View not found: ' . $key);
        }
        $view->setViewKey($key);
        $view->setViewOptions($options);
        return $view;
    }

    /**
     * Gets appropriate helper object specified by "$helper" parameter
     * 
     * @param string $helper Helper path or full qualified class name
     * @param boolean $dup If true always creates new object else retrives existing one if it is.
     * @return Uni_Core_Helper 
     */
    public static function getHelper($helper, $dup = FALSE) {
        if (!$dup) {
            $hKey = 'helpers/' . $helper;
            if (!array_key_exists($hKey, self::$registry) || !self::$registry[$hKey]) {
                self::$registry[$hKey] = Uni_Core_Bean::instantiate(self::getPath(), $helper, self::IF_HELPER, 1);
            }
            return self::$registry[$hKey];
        }
        return Uni_Core_Bean::instantiate(self::getPath(), $helper, self::IF_HELPER, 1);
    }

    /**
     * Loads application settings
     */
    public static function initializePreferences() {
        if (Uni_Core_Installer::isInstalled()) {
            self::$preferences = Uni_Core_Preferences::loadPreferences();
        }
    }

    /**
     * Retrieve setting data
     * 
     * @param string $key
     * @return string|FALSE
     */
    public static function getPreference($key) {
        if (empty(self::$preferences)) {
            self::initializePreferences();
        }
        if (array_key_exists($key, self::$preferences)) {
            return self::$preferences[$key];
        }
        return FALSE;
    }

    /**
     * Set application mode
     * 
     * @param string $mode
     */
    public static function setMode($mode) {
        self::$mode = $mode;
    }

    /**
     * Retrieve application mode
     * 
     * @return string 
     */
    public static function getMode() {
        return self::$mode;
    }

    /**
     * Retrieve request object
     * 
     * @return NULL|Zend_Controller_Request_Abstract
     */
    public static function getRequest() {
        if (NULL == self::$_request) {
            self::$_request = Zend_Controller_Front::getInstance()->getRequest();
        }
        return self::$_request;
    }

    /**
     * Retrieve theme url
     * 
     * @param string $url
     * @param string|FALSE $mode
     * @return string
     */
    public static function getThemeUrl($url = '', $mode = FALSE) {
        $baseUrl = self::getSiteUrl();
        if (!$mode) {
            $mode = self::$mode;
        }
        $package = self::getPreference($mode . '/design/package');
        $theme = self::getPreference($mode . '/design/theme');
        $themeUrls[] = array('url' => $baseUrl . 'theme/' . $mode . '/' . $package . '/' . $theme . '/', 'path' => APPLICATION_PATH . DS . '..' . DS . 'theme' . DS . $mode . DS . $package . DS . $theme . DS);
        $themeUrls[] = array('url' => $baseUrl . 'theme/' . $mode . '/core/default/', 'path' => APPLICATION_PATH . DS . '..' . DS . 'theme' . DS . $mode . DS . 'core' . DS . 'default' . DS);
        if (null !== $url) {
            $url = ltrim($url, '/\\');
        }
        foreach ($themeUrls as $themeUrl) {
            $path = $themeUrl['url'] . $url;
            if (file_exists($themeUrl['path'] . str_replace('/', DS, $url))) {
                break;
            }
        }
        return $path;
    }

    /**
     * Compose url with given parameters
     * 
     * @param string $url First "*" is for module if "*" is given then current module otherwise given module same as second "*" is for controller and last third "*" is for action
     * @param array $options Request parameters
     * @param boolean $reset Whether the old parameter will reset or pass 
     * @return string Composed url
     */
    public static function getUrl($url = '*/*/*', array $options = array(), $reset = TRUE) {
        if (self::$mode == Uni_Controller_Action::MODE_INSTALL) {
            $front = Zend_Controller_Front::getInstance();
            $finalUrl = 'http://' . $_SERVER['HTTP_HOST'] . $front->getBaseUrl() . '/';
            $rout['admin'] = self::$defaultAdminKey;
        } else {
            $finalUrl = self::getSiteUrl();
            $rout['admin'] = self::getAdminKey();
        }
        $urlParts = explode('/', $url);
        $req = self::getRequest();
        $module = lcfirst($req->getModuleName());
        if ($module == 'admin') {
            $module = $rout['admin'];
        }
        $parts = array('module', 'controller', 'action');
        $exParams = $req->getParams();
        $exParams['module'] = $module;
        for ($i = 0; $i < count($urlParts); $i++) {
            if ($urlParts[$i] == '*') {
                $urlParts[$i] = $exParams[$parts[$i]];
            } else if ($i == 0 && $urlParts[$i] == 'admin') {
                $urlParts[$i] = $rout['admin'];
            }
            unset($exParams[$parts[$i]]);
            unset($parts[$i]);
            $finalUrl = $finalUrl . (($i > 0) ? '/' : '') . $urlParts[$i];
        }
        foreach ($parts as $part) {
            unset($exParams[$part]);
        }
        foreach ($options as $k => $v) {
            $finalUrl = $finalUrl . '/' . $k . '/' . $v;
            unset($exParams[$k]);
        }
        if (!$reset) {
            foreach ($exParams as $k => $v) {
                $finalUrl = $finalUrl . '/' . $k . '/' . $v;
            }
        }
        return ltrim($finalUrl, '/\\');
    }

    /**
     * Retrieve string in camel case
     * 
     * Capitalizes the character after underscore and removes the underscore
     * 
     * @param string $key
     * @return string 
     */
    public static function getCamelCase($key) {
        $words = explode('_', $key);
        $propName = '';
        for ($i = 0; $i < count($words); $i++) {
            $propName = $propName . (($i == 0) ? $words[$i] : ucfirst($words[$i]));
        }
        return $propName;
    }

    /**
     * Retrieve directory path
     * 
     * @param string $path
     * @return string
     */
    public static function getDirectoryPath($path = '') {
        return realpath($path);
    }

    /**
     * Retrieve uploads directory path
     * 
     * @return string 
     */
    public static function getUploadDirectoryPath() {
        return self::getDirectoryPath(UPLOAD_DIR);
    }

    /**
     * Retrieve uploads url path
     * 
     * @return string
     */
    public static function getUploadDirectoryUrl() {
        return self::getSiteUrl() . UPLOAD_DIR;
    }

    /**
     * Retrieve extensions directory path
     * 
     * @return string 
     */
    public static function getExtensionDirectoryPath() {
        return self::getDirectoryPath(EXTENSION_DIR);
    }

    /**
     * Retrieve extensions url path
     * 
     * @return string
     */
    public static function getExtensionDirectoryUrl() {
        return self::getSiteUrl() . EXTENSION_DIR;
    }

    /**
     * Retrieve website base url
     * 
     * Automatically switches to secure url if enabled
     * 
     * @return string
     */
    public static function getSiteUrl() {
        if (self::$mode == Uni_Controller_Action::MODE_INSTALL && !Uni_Core_Installer::isInstalled()) {
            $front = Zend_Controller_Front::getInstance();
            return 'http://' . $_SERVER['HTTP_HOST'] . $front->getBaseUrl() . '/';
        } else if (self::isSecureUrlEnabled()) {
            return self::getSiteSecureUrl();
        }
        return self::getPreference('web/unsecure/base_url');
    }

    /**
     * Retrieve secure base url
     * 
     * @return string
     */
    public static function getSiteSecureUrl() {
        return self::getPreference('web/secure/base_url');
    }

    /**
     * Retrieve current logined member
     * 
     * @return Fox_Member_Model_Member
     */
    public static function getLoggedMember() {
        return self::getModel('member/session')->getLoginData();
    }

    /**
     * Retrieve admin path after base url
     * 
     * @return string
     */
    public static function getAdminKey() {
        $adminPath = self::getPreference('admin/url/key');
        return $adminPath ? $adminPath : self::$defaultAdminKey;
    }

    /**
     * Retrieve admin url
     * 
     * @return string
     */
    public static function getAdminUrl() {
        return self::getSiteUrl() . self::getAdminKey();
    }

    /**
     * Retrieve database tables prefix
     * 
     * @return string 
     */
    public static function getTablePrefix() {
        if (self::$tablePrefix === FALSE) {
            $doc = new DOMDocument();
            $doc->load(CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml');
            $xpath = new DOMXPath($doc);
            $dbParams = $xpath->query('/config/production/resources/db/params/prefix');
            foreach ($dbParams as $dbParam) {
                if ($dbParam->nodeType == XML_ELEMENT_NODE) {
                    self::$tablePrefix = $dbParam->nodeValue;
                }
            }
            if (self::$tablePrefix === FALSE) {
                self::$tablePrefix = '';
            }
        }
        return self::$tablePrefix;
    }

    /**
     * Retrieve table name with prefix
     * 
     * @param string $name
     * @return string 
     */
    public static function getTableName($name) {
        return self::getTablePrefix() . $name;
    }

    /**
     * Get logger
     * 
     * 
     */
    public static function getLog() {
        return self::$logger;
    }

    /**
     * Retrieve whether session storage type is database
     * 
     * @return boolean 
     */
    public static function isDatabaseSession() {
        $sessionStorage = Fox::getPreference('core/session/storage_type');
        return ($sessionStorage == 'database');
    }

    /**
     * Retrieve whether profiler is enabled
     * 
     * @return boolean
     */
    public static function isProfilerEnabled() {
        return Fox::getPreference('core/debug/profiler');
    }

    /**
     * Retrieve whether template path hint is enabled
     * 
     * @return boolean
     */
    public static function isEnableTemplateHint() {
        return (self::$mode == Uni_Controller_Action::MODE_WEB && Fox::getPreference('core/debug/template_hints'));
    }

    /**
     * Retrieve whether secure url is enabled
     * 
     * @return boolean
     */
    public static function isSecureUrlEnabled() {
        return (self::$mode == Uni_Controller_Action::MODE_WEB && self::getPreference('web/secure/use_in_frontend')) || (self::$mode == Uni_Controller_Action::MODE_ADMIN && self::getPreference('web/secure/use_in_admin'));
    }

    /**
     * Retrieve admin session timeout interval in seconds
     * 
     * @return int
     */
    public static function getAdminSessionInterval() {
        $sessionInterval = Fox::getPreference('admin/login/online_minutes_interval');
        return $sessionInterval > 0 ? ($sessionInterval * 60) : self::$defaultAdminSessionInterval;
    }

    /**
     * Retrieve member session timeout interval in seconds
     * 
     * @return int
     */
    public static function getMemberSessionInterval() {
        $sessionInterval = Fox::getPreference('member/login/online_minutes_interval');
        return $sessionInterval > 0 ? ($sessionInterval * 60) : self::$defaultMemberSessionInterval;
    }

    /**
     * Logging message with default level INFO
     * 
     * @param type $message
     * @param type $level
     */
    public static function log($message, $level = Zend_Log::INFO) {
        if (NULL == self::$logger) {
            self::$logger = Zend_Registry::get('Zend_Log');
        }
        self::$logger->log($message, $level);
    }

    /**
     * Translation according to locale
     * 
     * @param string $str
     * @return string Translated string, if appropriate translation found. Otherwise same string will return.
     */
    public function translate($str) {
        if (NULL == self::$translator) {
            self::$translator = Zend_Registry::get('translator');
            if (($locale = Zend_Registry::get('Zend_Locale'))) {
                self::$translator->setLocale($locale);
//            print_r(self::$translator->getOptions());
            }
        }
        return self::$translator->_($str);
    }
     public static function getFormatedDate($date) {
        return (($date != '0000-00-00' && $date != '0000-00-00 00:00:00') ? date('d M, Y', strtotime($date)) : '-');
    }

    public static function getFormatedTime($date) {
        return (($date != '00:00:00') ? date('g:i a', strtotime($date)) : '-');
    }

    public static function getFormatedDateTime($date) {
        return (($date != '0000-00-00' && $date != '0000-00-00 00:00:00') ? date('d M, Y g:i a', strtotime($date)) : '-');
    }
}

Fox::getPath();
