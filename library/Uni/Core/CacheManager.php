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
 * Class Uni_Core_CacheManager
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_CacheManager {
    /**
     * 
     * @var array 
     */
    protected static $cacheArr;
    /**
     * Creates cache settings
     * @return void
     */
    public static function createCacheSettings() {
        self::$cacheArr=array();
        $cacheDoc = new Uni_Data_XDOMDocument();
        $cacheDoc->preserveWhiteSpace = FALSE;
        $cacheDoc->formatOutput = TRUE;
        $ds = DIRECTORY_SEPARATOR;
        $cacheSettingsPath = CACHE_DIR . $ds . 'cache.settings';
        $cacheModel = Fox::getModel('core/cache');
        $collection = $cacheModel->getCollection();
        $root = $cacheDoc->createElement('cache');
        foreach ($collection as $row) {
            self::$cacheArr[$row['cache_code']] = $row['status'];
            $cData = $cacheDoc->createCDATASection('');
            $cData->appendData($row['status']);
            $entry = $cacheDoc->createElement('entry');
            $entry->setAttribute('key', $row['cache_code']);
            $root->appendChild($entry);
            $entry->appendChild($cData);
        }
        $cacheDoc->appendChild($root);
        $cacheDoc->save($cacheSettingsPath);
    }

    /**
     * Load cache settings
     * @return array
     */
    public static function loadCacheSettings() {
        if(!empty(self::$cacheArr)){
            return self::$cacheArr;
        }
        $cacheDoc = new Uni_Data_XDOMDocument();
        $ds = DIRECTORY_SEPARATOR;
        $cacheSettingsPath = CACHE_DIR . $ds . 'cache.settings';
        if (file_exists($cacheSettingsPath)) {
            $cacheDoc->load($cacheSettingsPath);
            if ($cacheDoc->documentElement->hasChildNodes()) {
                $nodeList = $cacheDoc->documentElement->childNodes;
                foreach ($nodeList as $n) {
                    if ($n->nodeType == XML_ELEMENT_NODE && ($key = $n->getAttribute('key'))) {
                        self::$cacheArr[$key] = $n->nodeValue;
                    }
                }
            }
        }else{
            self::createCacheSettings();
        }
        return self::$cacheArr;
    }
    
    /**
     * Clears layout cache
     * 
     * @return void
     */
    public static function clearLayoutCache() {
        $ds = DIRECTORY_SEPARATOR;
        $cachePath = CACHE_DIR . $ds . Fox_Core_Model_Cache::CACHE_CODE_LAYOUT;
        if (file_exists($cachePath)) {
            Uni_Util_File::deleteDirectoryAndFiles($cachePath);
        }
    }
    
    /**
     * Remove module cache
     * @return void     * 
     */
    public static function clearModuleCache(){
        $cache = Uni_Fox::getCache();
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }
    
}