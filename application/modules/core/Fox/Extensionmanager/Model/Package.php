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
 * Class Fox_Extensionmanager_Model_Package
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */

class Fox_Extensionmanager_Model_Package extends Uni_Core_Model {

    /**
     * Contains Package key
     * 
     * @var string
     */
    protected $packageKey;

    /**
     * Contains Url validator
     * 
     * @var string
     */
    protected $urlValidation = null;

    /**
     * Contains package errors
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Contains package status
     * 
     * @var string
     */
    protected $staus;

    /**
     * Contains package session
     * 
     * @var Fox_Extensionmanager_Model_Session
     */
    protected $session;

    /**
     * Constructs object of model using optional configuration
     * supplied by $config param
     *
     * @param array $config 
     */
    public function __construct($config = array()) {
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::DEFAULT_STATUS);
        $conf = Fox::getModel('extensionmanager/config');
        if ($conf->hasErrors()) {
            $this->errors = $this->errors + $conf->getErrors();
        }
        $this->setConfig($conf);
        parent::__construct($config);
    }

    /**
     * Set package key and validate it
     * 
     * @param String $key
     * @return Fox_Extensionmanager_Model_Package 
     */
    public function setKey($key = null) {
        $this->packageKey = $key;
        $this->validateKey();
        return $this;
    }

    /**
     * Get Package Key
     * 
     * @return string
     */
    public function getKey() {
        return $this->packageKey;
    }

    /**
     * Get Package Session
     * 
     * @return Fox_Extensionmanager_Model_Session
     */
    public function getSession() {
        if (!$this->session) {
            $this->session = Fox::getModel("extensionmanager/session");
        }
        return $this->session;
    }

    /**
     * Apply some validation for package key
     * 
     * @return Fox_Extensionmanager_Model_Package 
     */
    protected function validateKey() {
        if (!$this->packageKey) {
            $this->errors[] = "Package key can't be empty";
            return;
        }
        /* validation for key is valid Url */
        if (!($this->urlValidation instanceof Uni_Validate_Url)) {
            $this->urlValidation = new Uni_Validate_Url();
        }
        if (!$this->urlValidation->isValid($this->packageKey)) {
            $this->errors[] = "Invalid package key";
            return;
        }

        /* validation for valid host in key */
        $config = $this->getConfig();
        $urlParts = @parse_url($this->packageKey);
        if ($config->getHost() != $urlParts['host']) {
            $this->errors[] = "Invalid package key";
            return;
        }
        return $this;
    }

    /**
     * Prepare package information
     * 
     * @return Fox_Extensionmanager_Model_Package 
     */
    public function _prepareInfo() {
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::PREPARING);
        try {
            $client = new Zend_Http_Client();
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $adapter->setConfig(array(
                'curloptions' => array(
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_RETURNTRANSFER => FALSE,
                    CURLOPT_FOLLOWLOCATION => TRUE,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_TIMEOUT => 10
                )
            ));
            $client->setAdapter($adapter);
            $client->setUri($this->packageKey);
            $client->setMethod("POST");
            /* Paramater to post request */
            $client->setParameterPost(array(
                'packageKey' => $this->packageKey,
                'hashKey' => $this->getConfig()->getHashKey(),
            ));
            $response = $client->request("POST");
            $pkgInfo = Zend_Json::decode($response->getBody());
            if (isset($pkgInfo['success']) && $pkgInfo['success']) {
                $this->setPackageInfo($pkgInfo);
                $this->setStatus(Fox_Extensionmanager_Model_Package_Status::PREPARED);
                /* setting package to package session */
                $this->save();
            } else {
                $this->errors[] = "Access Denied!";
            }
        } catch (Exception $e) {
            /* $this->errors[] = $e->getMessage(); */
            $this->errors[] = "Unable to connect package store";
            $this->setStatus(Fox_Extensionmanager_Model_Package_Status::DEFAULT_STATUS);
        }
        return $this;
    }

    /**
     * Download package
     */
    public function downloadPackage() {
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::DOWNLOADING);
        try {
            $config = $this->getConfig();
            $pkgInfo = $this->getPackageInfo();
            $pkgPath = $config->getPackageDirPath();
            if (!file_exists($pkgPath)) {
                @mkdir($pkgPath, 0777, true);
            }
            $fileName = $pkgPath . DS . $pkgInfo['file_name'];
            $fileHandle = fopen($fileName, 'w');
            $this->setFileName($fileName);
            $curl_obj = curl_init();
            curl_setopt($curl_obj, CURLOPT_URL, $pkgInfo['download_url']);
            curl_setopt($curl_obj, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curl_obj, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl_obj, CURLOPT_FILE, $fileHandle);
            curl_setopt($curl_obj, CURLOPT_FOLLOWLOCATION, TRUE); //followlocation cannot be used when safe_mode/open_basedir are on
            curl_setopt($curl_obj, CURLOPT_SSL_VERIFYPEER, FALSE);
            if (curl_exec($curl_obj)) {
                $this->setStatus(Fox_Extensionmanager_Model_Package_Status::DOWNLOADED);
            } else {
                $this->setStatus(Fox_Extensionmanager_Model_Package_Status::ERROR);
                throw new Exception("Downloading failed.");
            }
            curl_close($curl_obj);
            @chmod($fileName, 0777);
            $this->save();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Install package
     */
    public function installPackage() {
        try {
            if ($this->getStatus() == Fox_Extensionmanager_Model_Package_Status::DOWNLOADED) {
                $this->setStatus(Fox_Extensionmanager_Model_Package_Status::INSTALLING);
                if ($this->validatePackage()) {//validating package
                    $this->_putSiteToMaintenance(); //putting site to maintenance mode
                    if ($this->extractIt($this->getFileName(), Fox::getDirectoryPath())) {
                        //code for after extracting package
                    } else {
                        throw new Exception("Failure while extracting.");
                    }
                } else {
                    throw new Exception("Invalid Package file.");
                }
            } else {
                throw new Exception("Invalid Package status " . $this->getStatus());
            }
            $this->setStatus(Fox_Extensionmanager_Model_Package_Status::INSTALLED);
        } catch (Exception $e) {
            $this->setStatus(Fox_Extensionmanager_Model_Package_Status::ERROR);
            throw new Exception($e->getMessage());
        }
        return $this->save();
    }

    /**
     * Reinstall package
     * 
     * @param string $key valid package key
     */
    public function reinstallPackage($key) {
        $config = $this->getConfig();
        if (!(file_exists($config->getInstalledPkgPath() . DS . $key . '.xml') && file_exists($config->getPackageDirPath() . DS . $key . '.zip'))) {
            throw new Exception("Package not found.");
        }
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::DOWNLOADED);
        $this->setFileName($config->getPackageDirPath() . DS . $key . '.zip');
        $this->installPackage();
    }

    protected function _putSiteToMaintenance() {
        //code for putting site on mainteinance mode
    }

    /**
     * Extract archieve file to destination 
     * 
     * @param string $zipFile zip filename
     * @param string $dest Destination directory path
     * @return boolean
     * @access protected
     */
    protected function extractIt($zipFile, $dest = NULL) {
        if ($zipFile) {
            /*setting default destination directory to Fox root */
            if (!$dest) $dest = Fox::getDirectoryPath();
            $zipArc = new ZipArchive();
            if ($zipArc->open($zipFile)) {
                $entries = array();
                for ($idx = 0; $idx < $zipArc->numFiles; $idx++) {
                    $entries[] = $zipArc->getNameIndex($idx);
                }
                $conf = $this->getConfig()->getConfiguration();
                $locateName = $zipArc->locateName($conf->package_file, ZIPARCHIVE::FL_NOCASE);
                if ($locateName && isset($entries[$locateName])) {
                    unset($entries[$locateName]); //removing extension xml from list
                }
                if ($zipArc->extractTo($dest, $entries)) {
                    $stream = $zipArc->getStream($conf->package_file);
                    $this->addTo($stream, "installed");//add package to installed directory
                    fclose($stream);
                    $zipArc->close();
                    foreach ($entries as $entry):
                        @chmod($dest . DS . $entry, 0777);
                    endforeach;
                    return true;
                }
                $zipArc->close();
            }
        }
        return false;
    }

    /**
     * Validate package 
     * 
     * @return boolean true if valid otherwise false
     * @access protected
     */
    protected function validatePackage() {
        $fileName = $this->getFileName();
        if ($fileName && !is_dir($fileName) && file_exists($fileName)) {
            $zip = zip_open($fileName);
            if (is_resource($zip)) {
                zip_close($zip);
                $zipArc = new ZipArchive();
                if ($zipArc->open($fileName) === TRUE) {
                    $conf = $this->getConfig()->getConfiguration();
                    /* looking for xml file inside package */
                    $locateName = $zipArc->locateName($conf->package_file, ZIPARCHIVE::FL_NOCASE);
                    if ($locateName) {
                        $stream = $zipArc->getStream($conf->package_file);
                        $this->addTo($stream, "downloaded");
                        fclose($stream);
                        $zipArc->close();
                        return true;
                    }
                    $zipArc->close();
                }
            }
        }
        return false;
    }

    /**
     * Adds stream data to target xml file
     * 
     * @access protected
     */
    protected function addTo($stream, $target = null) {
        if ($stream && $target && get_resource_type($stream) == "stream" && ($pkgInfo = $this->getPackageInfo())) {
            $config = $this->getConfig();
            $dir = $config->getDownloadedPkgPath();
            if ($target == "downloaded") {
                $dir = $config->getDownloadedPkgPath();
            } elseif ($target == "installed") {
                $dir = $config->getInstalledPkgPath();
            }
            if (!file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }
            $filename = $dir . DS . $pkgInfo['key'] . '.xml';
            $streamDest = fopen($filename, 'w+');
            stream_copy_to_stream($stream, $streamDest);
            fclose($streamDest);
            @chmod($filename, 0777);
        }
    }

    /**
     * Save package data to package session
     * 
     * @return Fox_Extensionmanager_Model_Package
     */
    public function save() {
        $this->getSession()->setPackage($this);
        return $this;
    }

    /**
     * Uninstall package
     * 
     * @param string $key valid package key
     */
    public function uninstallPackage($key) {
        $config = $this->getConfig();
        if (!(file_exists($config->getInstalledPkgPath() . DS . $key . '.xml'))) {
            throw new Exception("Installation not found.");
        }
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::UNINSTALLING);
        $packageXml = $config->getInstalledPkgPath() . DS . $key . '.xml';
        $document = new Uni_Data_XDOMDocument();
        $document->load($packageXml);

        /*removing package files*/
        $xpath = new DomXpath($document);
        foreach ($xpath->query('//item[@rel="file"]') as $file) {
            $filePath = Fox::getDirectoryPath() . $file->getAttribute("path");
            if (file_exists($filePath) && !is_dir($filePath)) {
                try {
                    @unlink($filePath);
                } catch (Exception $e) {}
            }
        }
        /*listing directories to remove*/
        $dirs = array();
        foreach ($xpath->query('//item[@rel="dir"]') as $file) {
            $dirPath = Fox::getDirectoryPath() . $file->getAttribute("path");
            if (file_exists($dirPath) && is_dir($dirPath)) {
                try {
                    $dirs[] = $dirPath;
                } catch (Exception $e) {}
            }
        }
        /*removing empty directories in $dir list*/
        if ($dirs) {
            $dirs = array_reverse($dirs);
            foreach ($dirs as $dir):
                @rmdir($dir);
            endforeach;
        }
        /*removing package from installed directory*/
        @unlink($packageXml);
        $this->clearCache();
        $this->setStatus(Fox_Extensionmanager_Model_Package_Status::UNINSTALLED);
    }

    /**
     * Clear cache
     */
    protected function clearCache() {
        Uni_Core_Preferences::loadPreferences(TRUE);
        Fox::initializePreferences();
        Uni_Core_CacheManager::clearLayoutCache();
    }

    /**
     * Package has errors
     * 
     * @return bool
     */
    public function hasErrors() {
        if (count($this->errors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Reset package
     * 
     * @return Fox_Extensionmanager_Model_Package 
     */
    public function reset() {
        $this->errors = array();
        $this->packageKey = null;
        $this->getSession()->unsetPackage();
        return $this;
    }

    /**
     * Get package errors
     * 
     * @return array 
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Add error to package
     * @param string $msg 
     * @return none 
     */
    public function addError($msg = "") {
        $this->errors[] = $msg;
    }

    /**
     * Set package status
     * 
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Get package current status
     * 
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Get current status text
     * 
     * @return string 
     */
    public function getStatusText() {
        $model = Fox::getModel('extensionmanager/package/status');
        $statuses = $model->_getOptionArray();
        return $statuses[$this->status];
    }

}