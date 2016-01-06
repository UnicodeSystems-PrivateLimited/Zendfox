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
 * Class Fox_Extensionmanager_Model_Generate_Package
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Generate_Package extends Uni_Core_Model {

    const PACKAGE_ROOT_ELEMENT = 'extensioninfo';
    const PACKAGE_NAME_ELEMENT = 'name';
    const PACKAGE_VERSION_ELEMENT = 'version';
    const PACKAGE_DATE_ELEMENT = 'date';
    const PACKAGE_STABILITY_ELEMENT = 'stability';
    const PACKAGE_DESCRIPTION_ELEMENT = 'description';
    const PACKAGE_SUMMARY_ELEMENT = 'summary';
    const PACKAGE_LICENSE_ELEMENT = 'license';
    const PACKAGE_RELEASE_NOTE_ELEMENT = 'releaseNote';
    const PACKAGE_CONTENTS_ELEMENT = 'contents';
    const PACKAGE_PROVIDERS_ELEMENT = 'providers';
    const PACKAGE_PROVIDERS_NOTE_ELEMENT = 'providerNote';

    /**
     * Package has valid data
     * 
     * @var bool 
     */
    protected $hasData = false;

    /**
     * Package errors
     * 
     * @var array 
     */
    protected $errors = array();

    /**
     * Package xml document
     * 
     * @var Uni_Data_XDOMDocument 
     */
    protected $packageXml;

    /**
     * Package configuration
     * 
     * @var Fox_Extensionmanager_Model_Config 
     */
    protected $config;

    /**
     * Package session
     * 
     * @var Fox_Extensionmanager_Model_Session 
     */
    protected $session;

    /**
     * Get package configuration
     * 
     * @return Fox_Extensionmanager_Model_Config
     */
    protected function getConfig() {
        if (!$this->config) {
            $this->config = Fox::getModel('extensionmanager/config');
        }
        return $this->config;
    }

    /**
     * Get package session
     */
    public function getSession() {
        if (!$this->session) {
            $this->session = Fox::getModel('extensionmanager/session');
        }
        return $this->session;
    }

    /**
     * Set data to package
     * 
     * @param array $data Package data.
     * @throws Exception If Invalid package data
     */
    public function setData(array $data) {
        try {
            if ($data) {
                if (!(isset($data["name"]) && $data["name"] && isset($data["description"]) && $data["description"]
                        && isset($data["summary"]) && $data["summary"] && isset($data["license"]) && $data["license"]
                        && isset($data["version"]) && $data["version"] && isset($data["stability"]) && $data["stability"])) {
                    throw new Exception("Invalid package data");
                }
                if (!Fox::getHelper("extensionmanager/data")->validateVersion($data["version"])) {
                    throw new Exception("Version field must contain value like x.x.x.x");
                }
                $this->setVersion($data["version"]);
                $this->setName($data["name"]);
                $this->setDescription($data["description"]);
                $this->setSummary($data["summary"]);
                $this->setLicense($data["license"]);

                if (isset($data["license_url"])) {
                    $this->setLicenseUrl($data["license_url"]);
                }

                $this->setStability($data["stability"]);

                if (isset($data["release_note"])) {
                    $this->setReleaseNote($data["release_note"]);
                }

                if (isset($data['providers']) && $data['providers']) {
                    $this->setProviders($data['providers']);
                } else {
                    throw new Exception("Invalid provider.");
                }

                if (isset($data['provider_note'])) {
                    $this->setProviderNote($data['provider_note']);
                }

                if (isset($data['content_tree_data']) && $data['content_tree_data']) {
                    $this->setContentTreeData($data['content_tree_data']);
                } else {
                    throw new Exception("Invalid package content");
                }
                $this->hasData = TRUE;
            } else {
                throw new Exception("Invalid package data.");
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Load package to package session
     * 
     * @param string $pkgName Valid generated package name.
     * @return Fox_Extensionmanager_Model_Generate_Package 
     */
    public function load($pkgName, $field=NULL) {
        $this->getSession()->setFormData(NULL);
        $pkgFile = $this->getGeneratedPkgDir() . DS . $pkgName;
        if (!($pkgName && !is_dir($pkgFile) && file_exists($pkgFile))) {
            throw new Exception("Invalid package.");
            return;
        }
        $info = pathinfo($pkgFile);
        if ($info["extension"] != "xml") {
            throw new Exception("Invalid package file.");
        }
        $doc = new DOMDocument();
        $doc->load($pkgFile);
        if ($doc->documentElement->nodeName != self::PACKAGE_ROOT_ELEMENT) {
            throw new Exception("Invalid package xml.");
        }
        $data = array();
        $nList = $doc->documentElement->childNodes;
        foreach ($nList as $n) {
            if ($n->nodeName == self::PACKAGE_RELEASE_NOTE_ELEMENT) {
                $data["release_note"] = $n->nodeValue;
            } elseif ($n->nodeName == self::PACKAGE_LICENSE_ELEMENT) {
                $data[$n->nodeName] = $n->nodeValue;
                $data["license_url"] = $n->getAttribute("url");
            } elseif ($n->nodeName == self::PACKAGE_PROVIDERS_NOTE_ELEMENT) {
                $data["provider_note"] = $n->nodeValue;
            } elseif ($n->nodeName == self::PACKAGE_PROVIDERS_ELEMENT) {
                $authorList = $n->childNodes;
                foreach ($authorList as $author):
                    if ($author->nodeName == "#text")
                        continue;
                    $infos = $author->childNodes;
                    foreach ($infos as $info):
                        if ($info->nodeName == "#text")
                            continue;
                        $data["providers"][$info->nodeName][] = $info->nodeValue;
                    endforeach;
                endforeach;
            }elseif ($n->nodeName == self::PACKAGE_CONTENTS_ELEMENT) {
                $destinationList = $n->childNodes;
                $treeDom = new Uni_Data_XDOMDocument();
                $treeDom->loadXML("<root></root>");
                foreach ($destinationList as $destination):
                    if ($destination->nodeName == "#text")
                        continue;
                    $treeDom->documentElement->appendChild($treeDom->importNode($destination, true));
                endforeach;
                $data["content_tree_data"] = trim(preg_replace('/<\?xml.*\?>/', '', $treeDom->saveXML(), 1));
            }elseif ($n->nodeName == self::PACKAGE_STABILITY_ELEMENT) {
                $stblOp = array_flip(Fox::getModel("extensionmanager/package/state")->_getOptionArray());
                $data[$n->nodeName] = $stblOp[$n->nodeValue];
            } else {
                $data[$n->nodeName] = $n->nodeValue;
            }
        }
        if ($data) {
            $this->setData($data);
            $this->getSession()->setFormData($data);
        }
        return $this;
    }

    /**
     * Get Directory path of generated package xml
     * 
     * @return string
     */
    public function getGeneratedPkgDir() {
        return Fox::getExtensionDirectoryPath() . DS . $this->getConfig()->getPackagerDir() . DS . $this->getConfig()->getPackagerGenDir();
    }

    /**
     * Get Directory path of compressed package
     * 
     * @return string
     */
    public function getGeneratedPkgCompressedDir() {
        return Fox::getExtensionDirectoryPath() . DS . $this->getConfig()->getPackagerDir() . DS . $this->getConfig()->getPackagerCompressedDir();
    }

    /**
     * Get Package xml
     * 
     * @return Uni_Data_XDOMDocument
     */
    protected function getPackageXml() {
        if (!($this->packageXml instanceof Uni_Data_XDOMDocument)) {
            $this->packageXml = new Uni_Data_XDOMDocument();
        }
        return $this->packageXml;
    }

    /**
     * Prepare providers
     * Add providers information to package xml
     * 
     * @return Fox_Extensionmanager_Model_Generate_Package 
     */
    protected function _prepareProviders() {
        $pkgXml = $this->getPackageXml();
        $extInfo = $pkgXml->documentElement;
        $providers = Uni_Data_XDOMDocument::createNode(self::PACKAGE_PROVIDERS_ELEMENT, array(), $pkgXml);
        $providerNote = Uni_Data_XDOMDocument::createNode(self::PACKAGE_PROVIDERS_NOTE_ELEMENT, array(), $pkgXml);
        $providerNote->appendChild($pkgXml->createTextNode($this->getProviderNote()));
        $data = $this->getProviders();
        $names = $data["name"];
        $emails = $data["email"];
        foreach ($names as $k => $v):
            $author = Uni_Data_XDOMDocument::createNode('author', array(), $pkgXml);
            $name = Uni_Data_XDOMDocument::createNode('name', array(), $pkgXml);
            $name->appendChild($pkgXml->createTextNode($v));
            $email = Uni_Data_XDOMDocument::createNode('email', array(), $pkgXml);
            $email->appendChild($pkgXml->createTextNode($emails[$k]));
            $author->appendChild($name);
            $author->appendChild($email);
            $providers->appendChild($author);
        endforeach;
        $extInfo->appendChild($providers);
        $extInfo->appendChild($providerNote);
        return $this;
    }

    /**
     * Prepare contents
     * 
     * Add contents information to package xml
     */
    protected function _prepareContentsTree() {
        $pkgXml = $this->getPackageXml();
        $extInfo = $pkgXml->documentElement;
        $contents = Uni_Data_XDOMDocument::createNode(self::PACKAGE_CONTENTS_ELEMENT, array(), $pkgXml);
        $extInfo->appendChild($contents);
        $treeDom = Uni_Data_XDOMDocument::loadXML($this->getContentTreeData());
        $xpath = new DomXpath($treeDom);

        /* traversing tree dom to remove state attribute */
        foreach ($xpath->query('//item[@state]') as $rowNode) {
            $rowNode->removeAttribute("state");
        }

        $nodes = $treeDom->documentElement->childNodes;
        foreach ($nodes as $node):
            $n = $pkgXml->importNode($node, true);
            $contents->appendChild($n);
        endforeach;
        /* renaming node to "items" that have "root" */
        $i = 0;
        while (($list = $pkgXml->getElementsByTagName("root"))) {
            if ($i++ > 10000)
                break;
            try {
                if ($rootN = $list->item(0)) {
                    $newNode = Uni_Data_XDOMDocument::createNode("items", array(), $pkgXml);
                    foreach ($rootN->attributes as $attribute) {
                        $newNode->setAttribute($attribute->nodeName, $attribute->nodeValue);
                    }
                    foreach ($rootN->childNodes as $ee):
                        echo $ee->nodeName;
                        $newNode->appendChild($ee->CloneNode(true));
                    endforeach;
                    $rootN->parentNode->replaceChild($newNode, $rootN);
                }else {
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
     * Prepare package xml
     * 
     * @return bool
     */
    protected function preparePackage() {
        $pkgXml = $this->getPackageXml();
        /* generating package nodes */
        $extInfo = Uni_Data_XDOMDocument::createNode(self::PACKAGE_ROOT_ELEMENT, array(), $pkgXml);
        $name = Uni_Data_XDOMDocument::createNode(self::PACKAGE_NAME_ELEMENT, array(), $pkgXml);
        $version = Uni_Data_XDOMDocument::createNode(self::PACKAGE_VERSION_ELEMENT, array(), $pkgXml);
        $description = Uni_Data_XDOMDocument::createNode(self::PACKAGE_DESCRIPTION_ELEMENT, array(), $pkgXml);
        $summary = Uni_Data_XDOMDocument::createNode(self::PACKAGE_SUMMARY_ELEMENT, array(), $pkgXml);
        $license = Uni_Data_XDOMDocument::createNode(self::PACKAGE_LICENSE_ELEMENT, array(), $pkgXml);
        $releaseNote = Uni_Data_XDOMDocument::createNode(self::PACKAGE_RELEASE_NOTE_ELEMENT, array(), $pkgXml);
        $stability = Uni_Data_XDOMDocument::createNode(self::PACKAGE_STABILITY_ELEMENT, array(), $pkgXml);
        $date = Uni_Data_XDOMDocument::createNode(self::PACKAGE_DATE_ELEMENT, array(), $pkgXml);

        $name->appendChild($pkgXml->createTextNode($this->getName()));
        $version->appendChild($pkgXml->createTextNode($this->getVersion()));
        $summary->appendChild($pkgXml->createTextNode($this->getSummary()));
        $description->appendChild($pkgXml->createTextNode($this->getDescription()));

        $license->appendChild($pkgXml->createTextNode($this->getLicense()));
        if ($this->getLicenseUrl()) {
            $license->setAttribute("url", $this->getLicenseUrl());
        }
        $releaseNote->appendChild($pkgXml->createTextNode($this->getReleaseNote()));
        if ($this->getStability()) {
            $stability->appendChild($pkgXml->createTextNode(Fox::getModel("extensionmanager/package/state")->getOptionText($this->getStability())));
        }
        $date->appendChild($pkgXml->createTextNode(Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s')));

        $nodes = array($name, $version, $summary, $description, $license, $releaseNote, $stability, $date);
        foreach ($nodes as $node):
            $extInfo->appendChild($node);
        endforeach;
        $pkgXml->appendChild($extInfo);
        if ($this->getProviders()) {
            $this->_prepareProviders();
        }

        if ($this->getContentTreeData()) {
            $this->_prepareContentsTree();
        } else {
            throw new Exception("Unknown contents");
        }
        return true;
    }

    /**
     * Save package data to xml
     * 
     * @return bool returns true if data saved successfully. Otherwise false
     * @throws Exception if Package has invalid data
     */
    public function save() {
        if ($this->hasErrors() || !$this->hasData) {
            throw new Exception("Package has invalid data.");
            return false;
        }
        if ($this->preparePackage()) {
            $fileName = '' . str_replace(' ', '_', trim($this->getName())) . '-' . $this->getVersion() . '.xml';
            $this->setPackageName($fileName);
            $path = $this->getGeneratedPkgDir();
            if (!file_exists($path)) {
                @mkdir($path, 0777, true);
            }
            $filename = $path . DS . $fileName;
            $this->getPackageXml()->save($filename);
            @chmod($filename, 0777);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete package
     * 
     * @return bool
     */
    public function delete($condition=NULL) {
        $data = $this->getSession()->getFormData();
        if (!($data && isset($data["name"]) && $data["name"])) {
            throw new Exception("No package loaded.");
        }
        $pkgFileName = $this->getGeneratedPkgDir() . DS . str_replace(' ', '_', $data["name"]) . '-' . $data["version"] . '.xml';
        if (!is_dir($pkgFileName) && file_exists($pkgFileName)) {
            unlink($pkgFileName);
        } else {
            throw new Exception("Package Not found.");
        }
        $pkgZipName = $this->getGeneratedPkgCompressedDir() . DS . str_replace(' ', '_', $data["name"]) . '-' . $data["version"] . '.zip';
        if (!is_dir($pkgZipName) && file_exists($pkgZipName)) {
            unlink($pkgZipName);
        }
        $this->getSession()->setFormData(NULL);
        return true;
    }

    /**
     * Generate package compressed file
     * 
     * @return bool
     */
    public function generate() {
        $data = $this->getSession()->getFormData();
        $conf = $this->getConfig()->getConfiguration();
        if (!($data && isset($data["name"]) && $data["name"])) {
            throw new Exception("No package loaded.");
        }
        $path = $this->getGeneratedPkgCompressedDir();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $pkgFileName = $path . DS . str_replace(' ', '_', $data["name"]) . '-' . $data["version"] . '.zip';
        if (!extension_loaded('zip')) {
            throw new Exception("Extension zip is not loaded.");
        }
        /* Opening zip file */
        $zip = new ZipArchive();
        if (!$zip->open($pkgFileName, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $treeDom = Uni_Data_XDOMDocument::loadXML($data["content_tree_data"]);
        foreach ($treeDom->documentElement->childNodes as $target):
            $this->zipIt($zip, $target->getAttribute("path"));
        endforeach;
        $pkgXml = str_replace('\\', DS, realpath($this->getGeneratedPkgDir() . DS . str_replace(' ', '_', $data["name"]) . '-' . $data["version"] . '.xml'));
        $zip->addFromString($conf->package_file, file_get_contents($pkgXml));
        @chmod($pkgFileName, 0777);
        $zip->close();
        return true;
    }

    /**
     * Puts sourse path to zip
     * 
     * @param ZipArchive $zip Destination
     * @param string $sourcePath Source
     * @return bool
     */
    protected function zipIt($zip, $sourcePath) {
        $source = str_replace('\\', DS, realpath(Fox::getDirectoryPath() . DS . $sourcePath));
        if (!file_exists($source)) {
            return false;
        }
        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', DS, $file);
                if (in_array(substr($file, strrpos($file, DS) + 1), array('.', '..')))
                    continue;
                $file = realpath($file);
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace(Fox::getDirectoryPath() . DS, '', $file . DS));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace(Fox::getDirectoryPath() . DS, '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(str_replace(Fox::getDirectoryPath() . DS, '', $source), file_get_contents($source));
        }
        return true;
    }

    /**
     * Package has errors
     * 
     * @return int
     */
    public function hasErrors() {
        return count($this->errors);
    }

    /**
     * Set error to package
     */
    public function setErrors($errors = array()) {
        if (!is_array($errors)) {
            $errors = (array) $errors;
        }
        $this->errors = array_merge($this->errors, $errors);
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
     * Get Xml of site directories/files
     * 
     * @param bool $cache if true gets xml from cache otherwise regenerates
     * @return string xml string
     */
    public function getSiteXml($cache = true) {
        $xml = '';
        $conf = $this->getConfig()->getConfiguration();
        $cachePath = Fox::getExtensionDirectoryPath() . DS . $conf->extensionmanager->dir_name. DS . $conf->extensionmanager->cache;
        if(!file_exists($cachePath)){
            @mkdir($cachePath, 0777, true);
        }
        $fileName = $cachePath.DS.$conf->extensionmanager->cache_file;
        if ($cache && file_exists($fileName)) {
            $xml = file_get_contents($fileName);
        } else {
            $path = Fox::getDirectoryPath();
            $Obj = new Fox_Extensionmanager_Model_Generate_DirectoryToXml($path);
            $Obj->traverseDirFiles($path);
            $Obj->setArray($Obj->arr);
            if (!($xml = $Obj->saveArrayToXml())) {
                throw new Exception("Unable to load content tree data.");
            }
            $dom = $Obj->getXMLDom();
            $dom->save($fileName);
            @chmod($fileName, 0777);
        }
        return trim(preg_replace('/<\?xml.*\?>/', '', $xml, 1));
    }

}