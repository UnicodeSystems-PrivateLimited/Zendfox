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
 * Class Fox_Extensionmanager_View_Admin_Packages
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Packages extends Uni_Core_Admin_View {

    /**
     * Contains package configuration
     * 
     * @var Fox_Extensionmanager_Model_Config
     */
    protected $config;
    
    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Get config
     * 
     * @return Fox_Extensionmanager_Model_Config 
     */
    protected function getConfig() {
        if (!$this->config) {
            $this->config = Fox::getModel("extensionmanager/config");
        }
        return $this->config;
    }

    /**
     * Get installed packages
     * 
     * @return array sorted list of installed packages 
     */
    public function getInstalledPackages() {
        $pkgPath = $this->getConfig()->getInstalledPkgPath();
        $pkgList = array();
        $noMod = array('.', '..');
        $doc = new DOMDocument();
        if (is_dir($pkgPath)) {
            $dp = opendir($pkgPath);
            while (($file = readdir($dp))) {
                if (!in_array($file, $noMod)) {
                    $doc->load($pkgPath . DS . $file);
                    if ($doc->documentElement->nodeName == Fox_Extensionmanager_Model_Generate_Package::PACKAGE_ROOT_ELEMENT && $doc->documentElement->childNodes) {
                        $data = array();
                        $nList = $doc->documentElement->childNodes;
                        foreach ($nList as $n) {
                            if (in_array($n->nodeName, array(Fox_Extensionmanager_Model_Generate_Package::PACKAGE_NAME_ELEMENT, Fox_Extensionmanager_Model_Generate_Package::PACKAGE_VERSION_ELEMENT, Fox_Extensionmanager_Model_Generate_Package::PACKAGE_STABILITY_ELEMENT))) {
                                $data[$n->nodeName] = $n->nodeValue;
                            }
                        }
                        if ($data) {
                            $data["key"] = str_replace(" ", "_", strtolower($data[Fox_Extensionmanager_Model_Generate_Package::PACKAGE_NAME_ELEMENT])) . "-" . $data[Fox_Extensionmanager_Model_Generate_Package::PACKAGE_VERSION_ELEMENT];
                            $pkgList[] = $data;
                        }
                    }
                }
            }
        }
        sort($pkgList);
        return $pkgList;
    }

    /**
     * Get package updaes
     * 
     * @param array $pkgList
     * @return boolean
     */
    public function getUpdates($pkgList = array()) {
        $param = array();
        if ($pkgList) {
            foreach ($pkgList as $pkg):
                $param[] = $pkg["key"];
            endforeach;
        }
        $config = $this->getConfig()->getConfiguration();
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
        $client->setUri($config->downloader->updates_url);
        $client->setMethod("POST");
        /* Paramater to post request */
        $client->setParameterPost(array(
            'keys' => implode(",", $param)
        ));
        $response = $client->request("POST");
        $result = Zend_Json::decode($response->getBody());
        if ($result["success"]) {
            return $result["updates"];
        } else {
            return FALSE;
        }
    }

}