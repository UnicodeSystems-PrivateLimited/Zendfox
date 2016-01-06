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
 * Class Uni_Core_Preferences
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Preferences {
    /**
     * System preferences
     *
     * @var array
     */
    private static $preferences = array();
    /**
     * Load module settings data
     * 
     * @param boolean $forceCreate
     * @return array 
     */
    public static function loadPreferences($forceCreate=FALSE) {
        if(!$forceCreate && !empty (self::$preferences)){
            self::$preferences; 
        }
        $ds = DIRECTORY_SEPARATOR;
        $preferenceFilePath = CACHE_DIR . $ds . Fox_Core_Model_Cache::CACHE_CODE_PREFERENCE;
        $fileName = 'preference.xml';
        self::$preferences = array();
        $xDom = new Uni_Data_XDOMDocument();
        $cacheSettings=Uni_Core_CacheManager::loadCacheSettings();
        $isCacheEnabled = (isset($cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_PREFERENCE])?$cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_PREFERENCE]:FALSE);
        if ($forceCreate || !$isCacheEnabled || !file_exists($preferenceFilePath . $ds . $fileName)) {
            if (!file_exists($preferenceFilePath)) {
                if (!@mkdir($preferenceFilePath, 0777, TRUE)) {
                    throw new Exception('"'.$preferenceFilePath.'" not found');
                }
            }
            $xDom->preserveWhiteSpace = false;        
            $xDom->formatOutput = true;
            $preferenceModel = Fox::getModel('core/preference');
            $collection = $preferenceModel->getCollection();
            $root = $xDom->createElement('preferences');
            foreach ($collection as $preference) {
                self::$preferences[$preference['name']] = $preference['value'];
                $cData = $xDom->createCDATASection('');
                $cData->appendData($preference['value']);
                $entry = $xDom->createElement('entry');
                $entry->setAttribute('key', $preference['name']);
                $root->appendChild($entry);
                $entry->appendChild($cData);
            }
            $xDom->appendChild($root);
            $doc = $xDom->save($preferenceFilePath. $ds . $fileName);
            @chmod($preferenceFilePath, 0777);
        } else {
            $xDom->load($preferenceFilePath. $ds . $fileName);
            if ($xDom->documentElement->hasChildNodes()) {
                $nodeList = $xDom->documentElement->childNodes;
                foreach ($nodeList as $n) {
                    if ($n->nodeType == XML_ELEMENT_NODE && ($key = $n->getAttribute('key'))) {
                        self::$preferences[$key] = $n->nodeValue;
                    }
                }
            }
        }
        unset($xDom);
        return self::$preferences;
    }

}