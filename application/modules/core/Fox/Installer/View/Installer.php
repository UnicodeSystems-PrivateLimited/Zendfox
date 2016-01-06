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
 * Class Fox_Installer_View_Installer
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Installer
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
 
class Fox_Installer_View_Installer extends Fox_Core_View_Template {

    /**
     * Form session data
     * 
     * @var array|NULL
     */
    private $_configurationData = NULL;

    /**
     * Public constructor
     */
    public function __construct() {
        parent::__construct();
        $this->_configurationData = Fox::getModel('installer/session')->getConfiguration();
    }

    /**
     * Get current step number
     * 
     * @return int
     */
    public function getCurrentStep() {
        return Fox::getModel('installer/session')->getCurrentStep();
    }

    /**
     * Get url for lisence agreement form
     * 
     * @return string
     */
    public function getLisenceUrl() {
        return Fox::getUrl('*/wizard/*');
    }

    /**
     * Get url for locale setup form
     * 
     * @return string
     */
    public function getLocalizationUrl() {
        return Fox::getUrl('*/wizard/locale');
    }

    /**
     * Get url for database configuration form
     * 
     * @return string
     */
    public function getConfigureUrl() {
        return Fox::getUrl('*/wizard/configure-post');
    }
    
    /**
     * Get license agreement text
     * 
     * @return string 
     */
    public function getLicenseHtml() {
            if(file_exists('license.html')){
        try{
        $fileHandle=fopen('license.html',"r");
        if($fileHandle){
        $licenseText=fread($fileHandle,filesize('license.html'));
        fclose($fileHandle);
        return $licenseText;
        }
        }catch(Exception $e){
            
        }
        }
    }

    /**
     * Get select box for locale
     * 
     * @return string 
     */
    public function getLocaleHtml() {
        $defaultLocale = Fox::getModel('installer/session')->getLocale();
        if (!$defaultLocale) {
            $defaultLocale = Fox_Core_Model_Resource::getDefaultLocale();
        }
        $select = $this->formSelect('locale', $defaultLocale, NULL, Fox_Core_Model_Resource::getAllowedLocales());
        return $select;
    }

    /**
     * Get select box for timezone
     * 
     * @return string 
     */
    public function getTimeZoneHtml() {
        $defaultTimezone = Fox::getModel('installer/session')->getTimezone();
        if (!$defaultTimezone) {
            $defaultTimezone = Fox_Core_Model_Resource::getDefaultTimezone();
        }
        $select = $this->formSelect('timezone', $defaultTimezone, NULL, Fox_Core_Model_Resource::getAllowedTimezones());
        return $select;
    }

    /**
     * Retrieve database host name from session data
     * 
     * @return string
     */
    public function getDbHost() {
        $defaultHost = 'localhost';
        if (isset($this->_configurationData['db_host']) && $this->_configurationData['db_host']) {
            $defaultHost = $this->_configurationData['db_host'];
        }
        return $defaultHost;
    }

    /**
     * Retrieve database name from session data
     * 
     * @return string
     */
    public function getDbName() {
        $defaultName = 'fox';
        if (isset($this->_configurationData['db_name']) && $this->_configurationData['db_name']) {
            $defaultName = $this->_configurationData['db_name'];
        }
        return $defaultName;
    }

    /**
     * Retrieve database username from session data
     * 
     * @return string
     */
    public function getDbUsername() {
        $defaultUsername = '';
        if (isset($this->_configurationData['db_username']) && $this->_configurationData['db_username']) {
            $defaultUsername = $this->_configurationData['db_username'];
        }
        return $defaultUsername;
    }

    /**
     * Retrieve database password from session data
     * 
     * @return string
     */
    public function getDbPassword() {
        $defaultPassword = '';
        if (isset($this->_configurationData['db_password']) && $this->_configurationData['db_password']) {
            $defaultPassword = $this->_configurationData['db_password'];
        }
        return $defaultPassword;
    }

    /**
     * Retrieve database table prefix from session data
     * 
     * @return string
     */
    public function getDbTablePrefix() {
        $defaultTablePrefix = '';
        if (isset($this->_configurationData['db_table_prefix']) && $this->_configurationData['db_table_prefix']) {
            $defaultTablePrefix = $this->_configurationData['db_table_prefix'];
        }
        return $defaultTablePrefix;
    }

    /**
     * Retrieve base url of website
     * 
     * @return string
     */
    public function getSiteBaseUrl() {
        $front = Zend_Controller_Front::getInstance();
        $defaultSiteUrl = 'http://' . $_SERVER['HTTP_HOST'] . $front->getBaseUrl() . '/';
        if (isset($this->_configurationData['site_base_url']) && $this->_configurationData['site_base_url']) {
            $defaultSiteUrl = $this->_configurationData['site_base_url'];
        }
        return $defaultSiteUrl;
    }

    /**
     * Retrieve secure base url of website
     * 
     * @return string
     */
    public function getSiteSecureBaseUrl() {
        $front = Zend_Controller_Front::getInstance();
        $defaultSiteSecureUrl = 'https://' . $_SERVER['HTTP_HOST'] . $front->getBaseUrl() . '/';
        if (isset($this->_configurationData['site_secure_base_url']) && $this->_configurationData['site_secure_base_url']) {
            $defaultSiteSecureUrl = $this->_configurationData['site_secure_base_url'];
        }
        return $defaultSiteSecureUrl;
    }

    /**
     * Retrieve whether secure url is enabled in frontend from session data
     * 
     * @return int
     */
    public function getSiteSecureUrlInFrontend() {
        return (isset($this->_configurationData['site_secure_url_in_frontend']) && $this->_configurationData['site_secure_url_in_frontend']);
    }

    /**
     * Retrieve whether secure url is enabled in backend from session data
     * 
     * @return int
     */
    public function getSiteSecureUrlInAdmin() {
        return (isset($this->_configurationData['site_secure_url_in_admin']) && $this->_configurationData['site_secure_url_in_admin']);
    }

    /**
     * Retrieve admin url key from session data
     * 
     * @return string
     */
    public function getAdminPath() {
        $defaultAdminPath = 'admin';
        if (isset($this->_configurationData['admin_path']) && $this->_configurationData['admin_path']) {
            $defaultAdminPath = $this->_configurationData['admin_path'];
        }
        return $defaultAdminPath;
    }

    /**
     * Retrieve session storage type from session data
     * 
     * @return string file|database
     */
    public function getSessionStorage() {
        $defaultSessionStorage = 'file';
        if (isset($this->_configurationData['session_storage']) && $this->_configurationData['session_storage']) {
            $defaultSessionStorage = $this->_configurationData['session_storage'];
        }
        return $defaultSessionStorage;
    }

}