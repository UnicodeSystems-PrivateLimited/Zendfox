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
 * Class Uni_Core_Installer
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
  */
class Uni_Core_Installer {

    /**
     * @var array
     */
    private static $installed = array();

    /**
     * Checks whether the application is installed
     * 
     * @return boolean TRUE if application is installed
     */
    public static function isInstalled() {
        if (empty(self::$installed)) {
            $systemConfigPath = CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml';
            self::$installed['status'] = FALSE;
            if (self::isSystemFileExists()) {
                $doc = new DOMDocument();
                $doc->load($systemConfigPath);
                $xpath = new DOMXPath($doc);
                $configData = $xpath->query('/config/install/date');
                foreach ($configData as $data) {
                    if ($data->nodeType == XML_ELEMENT_NODE) {
                        if ($data->nodeName == 'date') {
                            self::$installed['status'] = ($data->nodeValue > 0 && Fox::getModel('core/date')->getTimestamp() >= $data->nodeValue);
                        }
                    }
                }
            }
        }
        return self::$installed['status'];
    }
    
    /**
     * Checks whether the system.xml file exists and is readable
     * 
     * @return boolean TRUE if system.xml exists and is readable
     */
    public static function isSystemFileExists(){
        $systemConfigPath = CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml';
        return (file_exists($systemConfigPath) && is_readable($systemConfigPath));
    }

    /**
     * Saves data in system config file
     * 
     * @param array $data
     * @throws Exception if system config file does not exists
     */
    private static function saveSystemConfig($data) {
        $ds = DIRECTORY_SEPARATOR;
        $configPath = CONFIG_DIR . $ds . 'system.xml';
        $defaultConfigPath = CONFIG_DIR . $ds . 'system_default.xml';
        if (file_exists($defaultConfigPath)) {
            copy($defaultConfigPath, $configPath);
            chmod($configPath, 0777);
            $xdoc = new DOMDocument();
            $xdoc->preserveWhiteSpace = FALSE;
            $xdoc->formatOutput = TRUE;
            $xdoc->load($configPath);
            $xpath = new DOMXPath($xdoc);
            $dbParams = $xpath->query('/config/production/resources/db/params/*');
            foreach ($dbParams as $dbParam) {
                if ($dbParam->nodeType == XML_ELEMENT_NODE) {
                    switch ($dbParam->nodeName) {
                        case 'host':
                            $newNode = $xdoc->createElement('host');
                            $newNode->appendChild($xdoc->createCDATASection($data['db_host']));
                            break;
                        case 'username':
                            $newNode = $xdoc->createElement('username');
                            $newNode->appendChild($xdoc->createCDATASection($data['db_username']));
                            break;
                        case 'password':
                            $newNode = $xdoc->createElement('password');
                            $newNode->appendChild($xdoc->createCDATASection($data['db_password']));
                            break;
                        case 'dbname':
                            $newNode = $xdoc->createElement('dbname');
                            $newNode->appendChild($xdoc->createCDATASection($data['db_name']));
                            break;
                    }
                    $dbParam->parentNode->replaceChild($newNode, $dbParam);
                    $lastNode = $newNode;
                }
            }
            $prefixNode = $xdoc->createElement('prefix');
            $prefixNode->appendChild($xdoc->createCDATASection($data['db_table_prefix']));
            $lastNode->parentNode->appendChild($prefixNode);

            $phpSettingsData = $xpath->query('/config/production/phpSettings/*');
            foreach ($phpSettingsData as $setting) {
                if ($setting->nodeType == XML_ELEMENT_NODE) {
                    break;
                }
            }
            if ($setting) {
                $dateNode = $xdoc->createElement('date');
                $timezoneNode = $xdoc->createElement('timezone');
                $timezoneNode->appendChild($xdoc->createCDATASection(Fox::getModel('installer/session')->getTimezone()));
                $dateNode->appendChild($timezoneNode);
                $setting->parentNode->appendChild($dateNode);
            }

            $resourcesData = $xpath->query('/config/production/resources/locale');
            foreach ($resourcesData as $resource) {
                if ($resource->nodeType == XML_ELEMENT_NODE) {
                    break;
                }
            }
            if ($resource) {
                $defaultNode = $xdoc->createElement('default');
                $forceNode = $xdoc->createElement('force');
                $defaultNode->appendChild($xdoc->createCDATASection(Fox::getModel('installer/session')->getLocale()));
                $resource->appendChild($defaultNode);
                $forceNode->appendChild($xdoc->createCDATASection(TRUE));
                $resource->appendChild($forceNode);
            }
            $xdoc->save($configPath);
            @chmod($configPath, 0777);
        } else {
            throw new Exception('Path "' . realpath($defaultConfigPath) . '" was not found.');
        }
    }

    /**
     * Updates system config file
     */
    private static function updateSystemConfig() {
        $systemConfigPath = CONFIG_DIR . DIRECTORY_SEPARATOR . 'system.xml';
        if (file_exists($systemConfigPath)) {
            $xdoc = new DOMDocument();
            $xdoc->preserveWhiteSpace = FALSE;
            $xdoc->formatOutput = TRUE;
            $xdoc->load($systemConfigPath);
            $xpath = new DOMXPath($xdoc);
            $configData = $xpath->query('/config/*');
            foreach ($configData as $data) {
                if ($data->nodeType == XML_ELEMENT_NODE) {
                    break;
                }
            }
            if ($data) {
                $installNode = $xdoc->createElement('install');
                $dateNode = $xdoc->createElement('date');
                $dateNode->appendChild($xdoc->createCDATASection(Fox::getModel('core/date')->getTimestamp()));
                $installNode->appendChild($dateNode);
                $data->parentNode->appendChild($installNode);
                $xdoc->save($systemConfigPath);
            }
        }
    }

    /**
     * Checks whether server is compatible
     * 
     * @return array returns errors as array
     */
    public static function checkServerCompatiblity() {
        $extensions = array('dom', 'gd', 'hash', 'mcrypt', 'pcre', 'pdo', 'pdo_mysql', 'simplexml');
        $errors = array();
        if (version_compare(phpversion(), '5.2.0', '<')) {
            $errors[] = 'PHP version must be 5.2.0 or greater.';
        }
        if (!ini_get('safe_mode')) {
            preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $version);
            if (!empty($version) && version_compare($version[0], '4.1.0', '<')) {
                $errors[] = 'MySQL must be 4.1.0 or greater.';
            }
        } else {
            $errors[] = 'Safe Mode must be off.';
        }

        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = '"' . $extension . '"' . ' extension must be installed.';
            }
        }
        return $errors;
    }

    /**
     * Run installer
     */
    public static function runInstaller() {
        $data = Fox::getModel('installer/session')->getConfiguration();
        self::saveSystemConfig($data);
        Uni_Core_ModuleManager::installModules(TRUE);
        $model = Fox::getModel('core/preference');                
        $model->setName('web/design/package');
        $model->setValue('core');
        $model->save();
        $model->unsetData();
        $model->setName('web/design/theme');
        $model->setValue('default');
        $model->save();
        $model->unsetData();
        $model->setName('web/unsecure/base_url');
        $model->setValue($data['site_base_url']);
        $model->save();
        $model->unsetData();
        $model->setName('web/secure/base_url');
        $model->setValue($data['site_secure_base_url']);
        $model->save();
        $model->unsetData();
        $model->setName('web/secure/use_in_frontend');
        $model->setValue(isset($data['site_secure_url_in_frontend']) ? $data['site_secure_url_in_frontend'] : 0);
        $model->save();
        $model->unsetData();
        $model->setName('web/secure/use_in_admin');
        $model->setValue(isset($data['site_secure_url_in_admin']) ? $data['site_secure_url_in_admin'] : 0);
        $model->save();
        $model->unsetData();
        $model->setName('admin/url/key');
        $adminPath = preg_replace('/\s+/', ' ', $data['admin_path']);
        $adminPath=str_replace(' ', '_', $adminPath);
        $model->setValue($adminPath);
        $model->save();
        $model->unsetData();
        $model->setName('core/session/storage_type');
        $model->setValue($data['session_storage']);
        $model->save();
        $model->unsetData();
        $model->setName('general/website/name');
        $model->setValue('Zendfox');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/general/sender_name');
        $model->setValue('Owner');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/general/sender_email');
        $model->setValue('owner@example.com');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/custom1/sender_name');
        $model->setValue('Custom 1');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/custom1/sender_email');
        $model->setValue('custom1@example.com');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/custom2/sender_name');
        $model->setValue('Custom 2');
        $model->save();
        $model->unsetData();
        $model->setName('website_email_addresses/custom2/sender_email');
        $model->setValue('custom2@example.com');
        $model->save();
        $model->unsetData();
        $model->setName('contact/reply/name');
        $model->setValue('Support');
        $model->save();
        $model->unsetData();
        $model->setName('contact/reply/email');
        $model->setValue('support@example.com');
        $model->save();
        $model->unsetData();
        $model->setName('contact/receiver/email');
        $model->setValue('support@example.com');
        $model->save();
        $model->unsetData();
    }

    /**
     * Finish setup
     */
    public function finishSetup() {
        $backendData = Fox::getModel('installer/session')->getBackend();
        $backendData['role_id'] = 1;
        $backendData['status'] = Fox_Admin_Model_User::USER_STATUS_ENABLED;
        $backendData['password'] = md5($backendData['password']);
        $model = Fox::getModel('admin/user');
        $model->setData($backendData);
        $model->save();
        $model->unsetData();
        self::updateSystemConfig();
    }

}