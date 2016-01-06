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
 * Class Fox_Core_Model_Date
 * 
 * @uses Zend_Date
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Date extends Zend_Date {

    /**
     * Get current date
     * 
     * @param string|NULL $format Valid php formats
     * @return string 
     */
    public static function getCurrentDate($format=NULL) {
        if ($format) {
            Fox_Core_Model_Date::setOptions(array('format_type' => 'php'));
        }
        $date = new Fox_Core_Model_Date();
        return $date->toString($format);
    }

    /**
     * Returns the locale date
     * 
     * @param string|integer|Zend_Date|array $timestamp OPTIONAL Date value or value of date part to set ,depending on $part. If null the actual time is set
     * @param string|NULL $formatType Valid php formats
     * @param string|NULL $locale Uses default locale if NULL
     * @return string 
     */
    public static function __toLocaleDate($timestamp, $formatType=NULL, $locale=NULL) {
        if ($formatType) {
            Fox_Core_Model_Date::setOptions(array('format_type' => 'php'));
        }
        $date = new Fox_Core_Model_Date($timestamp, Zend_Date::TIMESTAMP, $locale);        
        return $date->toString($formatType);
    }

    /**
     * Get formatted date
     * 
     * @param Fox_Core_Model_Date $date
     * @param string $formatType Valid php formats
     * @param string|NULL $locale Uses default locale if NULL
     * @return string
     */
    public function formatDate(Fox_Core_Model_Date $date, $formatType, $locale=NULL) {
        Fox_Core_Model_Date::setOptions(array('format_type' => 'php'));
        return $date->toString($formatType);
    }

}