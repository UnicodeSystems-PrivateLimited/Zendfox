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
 * Class Fox_Extensionmanager_Model_Config
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Config extends Uni_Core_Model {

    const CONFIG_FILE = "config.xml";
    const CONFIG_SECTION = "managerdata";

    /**
     * Config object
     * 
     * @var Zend_Config_Xml
     */
    protected $config;

    /**
     * Path to configuration xml
     * 
     * @var string
     */
    protected $configPath;

    /**
     * Contains config errors
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Public Constructor
     */
    public function __construct() {
        $this->configPath = Fox::getExtensionDirectoryPath() . DS . self::CONFIG_FILE;
        if (!file_exists($this->configPath)) {
            $msg = "Extensionmanager config File doesn't exists";
            $this->errors[] = $msg;
            throw new Exception($msg);
        } else {
            $this->_init();
        }
    }

    /**
     * Initialize config
     */
    protected function _init() {
        try {
            $this->config = new Zend_Config_Xml($this->configPath, self::CONFIG_SECTION);
            if ($this->config->host) {
                $this->setHost($this->config->host);
            }
            $this->setIsSecure($this->config->secure ? true : false);
            $this->setHashKey($this->config->hashkey);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get package directory path
     * 
     * @return string
     */
    public function getPackageDirPath() {
        return Fox::getExtensionDirectoryPath() . DS . $this->getDownloaderDir() . DS . $this->config->downloader->compressed_dir;
    }

    /**
     * Get packager directory name
     * 
     * @return string
     */
    public function getPackagerDir() {
        return $this->config->extensionmanager->dir_name;
    }

    /**
     * Get packager compressed directory name
     * 
     * @return string
     */
    public function getPackagerCompressedDir() {
        return $this->config->extensionmanager->compressed_dir;
    }

    /**
     * Get packager gen directory name
     * 
     * @return string
     */
    public function getPackagerGenDir() {
        return $this->config->extensionmanager->generated_dir;
    }

    /**
     * Get Downloader directory name
     * 
     * @return string
     */
    public function getDownloaderDir() {
        return $this->config->downloader->dir_name;
    }

    /**
     * Get Downloader Compressed directory name
     * 
     * @return string
     */
    public function getDownloaderCompressedDir() {
        return $this->config->downloader->compressed_dir;
    }

    /**
     * Get Downloaded package directory path
     * 
     * @return string
     */
    public function getDownloadedPkgPath() {
        return Fox::getExtensionDirectoryPath() . DS . $this->getDownloaderDir() . DS . $this->config->downloader->downloaded_dir;
    }

    /**
     * Get Installed package directory path
     * 
     * @return string
     */
    public function getInstalledPkgPath() {
        return Fox::getExtensionDirectoryPath() . DS . $this->getDownloaderDir() . DS . $this->config->downloader->installed_dir;
    }

    /**
     * Checks for config errors
     * 
     * @return int number of errors
     */
    public function hasErrors() {
        return count($this->errors);
    }

    /**
     * Get config errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get config
     * 
     * @return Zend_Config_Xml
     */
    public function getConfiguration() {
        if (!$this->hasErrors()) {
            return $this->config;
        } else {
            return null;
        }
    }

}