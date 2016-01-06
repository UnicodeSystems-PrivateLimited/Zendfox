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
 * Class Uni_Core_ModuleManager
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_ModuleManager {

    /**
     * Module list
     * 
     * @var array
     */
    protected static $modules;

    /**
     * Contains core modules
     * 
     * @var array 
     */
    protected static $coreModules = array('core', 'admin', 'cms', 'installer', 'backup');

    /**
     * Contains active modules
     * 
     * @var array 
     */
    private static $activeModules = array();

    /**
     * contains installed modules
     * 
     * @var array
     */
    private static $installedModules = array();

    /**
     * @var array 
     */
    private static $initialModules = array();
    
    /**
     *
     * @var type 
     */
    private static $sqlSetup;

    /**
     * Get all modules
     * 
     * @return array
     */
    public static function getModules() {
        $moduleList = array();
        self::$modules = array();
        $noMod = array('.', '..');
        $doc = new DOMDocument();
        if (is_dir(MODULE_CONFIG_DIR)) {
            $dp = opendir(MODULE_CONFIG_DIR);
            while (($file = readdir($dp))) {
                if (!in_array($file, $noMod)) {
                    $doc->load(MODULE_CONFIG_DIR . DIRECTORY_SEPARATOR . $file);
                    if ($doc->documentElement->childNodes) {
                        $nList = $doc->documentElement->childNodes;
                        foreach ($nList as $n) {
                            if ($n->nodeName == 'module' && ($name = $n->getAttribute('name')) && ($nameSpc = $n->getAttribute('namespace'))) {
                                $moduleList[$name] = array('package' => $n->getAttribute('package'), 'status' => ((strtolower($n->getAttribute('enabled')) == 'true') ? 1 : 0), 'version' => $n->getAttribute('version'), 'namespace' => $nameSpc);
                            }
                        }
                    }
                }
            }
        }
        return $moduleList;
    }

    /**
     * Get active modules
     * 
     * @return array
     */
    public static function getActiveModules() {
        $moduleList = self::getModules();
        $activeModules = array();
        foreach ($moduleList as $key => $value) {
            if (strtolower($value['status']) == Fox_Core_Model_Module::STATUS_ENABLED) {
                $activeModules[$key] = $value;
            }
        }
        return $activeModules;
    }

    /**
     * Install modules
     * 
     * @param boolean $isInstaller 
     */
    public static function installModules($isInstaller = FALSE) {
        self::$sqlSetup = new Uni_Core_Setup_Sql();
        self::$installedModules = array();
        self::$activeModules = self::getActiveModules();
        if (!$isInstaller) {
            $moduleModel = Fox::getModel('core/module');
            $collection = $moduleModel->getInstalledModules();
            foreach ($collection as $module) {
                self::$installedModules[] = $module['name'];
            }
        }
        $modulesArr = array_diff(array_keys(self::$activeModules), self::$installedModules);
        foreach ($modulesArr as $mdl) {
            self::$initialModules[$mdl] = $mdl;
        }
        while (count(self::$initialModules) > 0) {
            foreach (self::$initialModules as $moduleEle) {
                break;
            }
            $depQueue = array($moduleEle);
            while (count($depQueue) > 0) {
                $dependents = self::getDependents($depQueue[count($depQueue) - 1]);
                $ready = FALSE;
                if (empty($dependents)) {
                    $ready = TRUE;
                } else {
                    $depUninstCount = 0;
                    foreach ($dependents as $dependent) {
                        if (!self::isInstalled($dependent)) {
                            $depUninstCount++;
                            if (in_array($dependent, $depQueue)) {
                                throw new Exception('Circular module dependency detected in "' . $dependent . '"');
                            } else {
                                $depQueue[] = $dependent;
                                break;
                            }
                        }
                    }
                    if ($depUninstCount == 0) {
                        $ready = TRUE;
                    }
                }
                if ($ready) {
                    $cModule = array_pop($depQueue);
                    self::installModule($cModule);
                }
            }
        }
        foreach (self::$activeModules as $moduleName => $moduleArr) {
            self::runSqlUpgrade($moduleName, $moduleArr['namespace'], $moduleArr['package']);
        }
    }

    /**
     * Get dependent modules
     * 
     * @param string $module module name
     * @return array
     */
    private static function getDependents($module) {
        $dependentModules = array();
        if (($doc = Uni_Fox::loadModuleConfig($module))) {
            if ($doc->documentElement->childNodes) {
                $nList = $doc->documentElement->childNodes;
                foreach ($nList as $n) {
                    if ($n->nodeName == 'dependents') {
                        $dependents = $n->childNodes;
                        foreach ($dependents as $dependent) {
                            if ($dependent->nodeName == 'dependent') {
                                $dependentModules[] = $dependent->nodeValue;
                            }
                        }
                    }
                }
            }
        }
        return $dependentModules;
    }

    /**
     * Checks whether the module is installed
     * 
     * @param string $module module name
     * @return boolean TRUE if module is installed
     */
    private static function isInstalled($module) {
        return in_array($module, self::$installedModules);
    }

    /**
     * Install module
     * 
     * @param string $module module name
     */
    private static function installModule($module) {
        self::$installedModules[] = $module;
        if (array_key_exists($module, self::$initialModules)) {
            $sqlFiles = array();
            $moduleModel = Fox::getModel('core/module');
            $dir = APPLICATION_PATH . DS . 'modules' . DS . self::$activeModules[$module]['package'] . DS . self::$activeModules[$module]['namespace'] . DS . $module . DS . 'Setup';
            if (file_exists($dir)) {
            $directoryHandle = opendir($dir);
            while ($contents = readdir($directoryHandle)) {
                $path = $dir . DS . $contents;
                if ($contents != '.' && $contents != '..' && is_file($path)) {
                    $fileParts = explode('.php', $contents);
                    if (count($fileParts) > 1) {
                        $versionParts = explode('-', $fileParts[0]);
                        if (count($versionParts) > 1) {
                            $sqlFiles[] = $versionParts[1];
                        }
                    }
                }
            }
            if (count($sqlFiles)) {
                usort($sqlFiles, 'version_compare');
                foreach ($sqlFiles as $sqlVersion) {
                    self::$sqlSetup->install($dir, $sqlVersion);
                    $moduleModel->load($module);
                    $moduleModel->setSqlVersion($sqlVersion);
                        $moduleModel->setName($module);
                        $moduleModel->setVersion(self::$activeModules[$module]['version']);
                        $moduleModel->setPackage(self::$activeModules[$module]['package']);
                        $moduleModel->setNamespace(self::$activeModules[$module]['namespace']);
                        $moduleModel->setStatus(Fox_Core_Model_Module::STATUS_ENABLED);
                    $moduleModel->save();
                    $moduleModel->unsetData();
                }
            }
            }
            if (!count($sqlFiles)) {
                $moduleModel->setSqlVersion(self::$activeModules[$module]['version']);
                $moduleModel->setName($module);
                $moduleModel->setVersion(self::$activeModules[$module]['version']);
                $moduleModel->setPackage(self::$activeModules[$module]['package']);
                $moduleModel->setNamespace(self::$activeModules[$module]['namespace']);
                $moduleModel->setStatus(Fox_Core_Model_Module::STATUS_ENABLED);
                $moduleModel->save();
                $moduleModel->unsetData();
            }
            unset(self::$initialModules[$module]);
            Uni_Core_ResourceManager::addResources($module);
            self::saveSettings($module);
        }
    }

    /**
     * Run all the sql upgrades
     * 
     * @param string $module module name
     */
    public static function runSqlUpgrade($module, $namespace, $package) {
        $sqlFiles = array();
        $moduleModel = Fox::getModel('core/module');
        $moduleModel->load($module);
        $previousSqlVersion = $moduleModel->getSqlVersion();
        $dir = APPLICATION_PATH . DS . 'modules' . DS . $package . DS . $namespace . DS . $module . DS . 'Setup';
        if (file_exists($dir)) {
        $directoryHandle = opendir($dir);
        while ($contents = readdir($directoryHandle)) {
            $path = $dir . DS . $contents;
            if ($contents != '.' && $contents != '..' && is_file($path)) {
                $fileParts = explode('.php', $contents);
                if (count($fileParts) > 1) {
                    $versionParts = explode('-', $fileParts[0]);
                        if ((count($versionParts) > 1) && $versionParts[1] && (!$previousSqlVersion || (version_compare($versionParts[1], $previousSqlVersion) > 0))) {
                        $sqlFiles[] = $versionParts[1];
                    }
                }
            }
        }
        if (count($sqlFiles)) {
            usort($sqlFiles, 'version_compare');
            foreach ($sqlFiles as $sqlVersion) {
                self::$sqlSetup->install($dir, $sqlVersion);
                $moduleModel->load($module);
                $moduleModel->setSqlVersion($sqlVersion);
                $moduleModel->save();
                $moduleModel->unsetData();
            }
        }
        }
    }

    /**
     * Update module enabled status
     * 
     * @param array $modules module data
     */
    public static function updateModuleStatus($modules) {
        $model = Fox::getModel('core/module');
        foreach ($modules as $moduleName => $status) {
            $model->load($moduleName);
            $model->setStatus($status);
            $model->save();
            $model->unsetData();
        }
    }

    /**
     * Save module settings
     * 
     * @param string $module module name
     */
    private static function saveSettings($module) {
        if (($doc = Uni_Fox::loadModuleSetting($module))) {
            $xPath = new DOMXPath($doc);
            $items = $xPath->query('/settings/items/item');
            if ($items->length) {
                $preferenceModel = Fox::getModel('core/preference');
                foreach ($items as $item) {
                    $itemName = $item->getAttribute('name');
                    if ($item->childNodes->length) {
                        foreach ($item->childNodes as $sections) {
                            if ($sections->nodeName == 'sections') {
                                foreach ($sections->childNodes as $section) {
                                    if ($section->nodeName == 'section') {
                                        $sectionName = $section->getAttribute('name');
                                        foreach ($section->childNodes as $field) {
                                            if ($field->nodeName == 'field') {
                                                $fieldName = $field->getAttribute('name');
                                                foreach ($field->childNodes as $defaultNode) {
                                                    if ($defaultNode->nodeName == 'default') {
                                                        try {
                                                            $preferenceModel->load($itemName . '/' . $sectionName . '/' . $fieldName, 'name');
                                                            $preferenceModel->setName($itemName . '/' . $sectionName . '/' . $fieldName);
                                                            $preferenceModel->setValue($defaultNode->nodeValue);
                                                            $preferenceModel->save();
                                                            $preferenceModel->unsetData();
                                                        } catch (Exception $e) {
                                                            Fox::getModel('core/session')->addError($e->getMessage());
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}