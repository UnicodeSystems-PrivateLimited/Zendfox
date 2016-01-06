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
 * Class Fox_Core_Model_Preference
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Preference extends Uni_Core_Model {
    const CORE_UPLOAD_FOLDER='core';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'core_preferences';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get setting data
     * 
     * @param string $key
     * @return array|FALSE 
     */
    public function getPreferences($key) {
        if ($key) {
            $select = $this->select()->where('name LIKE ?', "$key/%");
            return $this->fetchAll($select)->toArray();
        } else {
            return FALSE;
        }
    }

    /**
     * Prepares and returns setting data
     * 
     * @param string $item
     * @return array 
     */
    public function getPreferenceData($item) {
        $data = array();
        $preferences = $this->getPreferences($item);
        if ($preferences) {
            foreach ($preferences as $key) {
                $keyVal = explode('/', $key['name']);
                if (count($keyVal) > 2) {
                    $data[$item][$keyVal[1]][$keyVal[2]] = $key['value'];
                }
            }
        }
        return $data;
    }

}