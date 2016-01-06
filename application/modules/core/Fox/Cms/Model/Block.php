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
 * Class Fox_Cms_Model_Block
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_Model_Block extends Uni_Core_Model {
    const STATUS_DISABLED=0;
    const STATUS_ENABLED=1;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'cms_block';
        parent::__construct();
    }

    /**
     * Get all status
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if identifier key already exists
     */
    protected function _preSave(array $data) {
        if ($this->isDuplicateIdentifierKey($data['identifier_key'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['identifier_key'] . "' already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the identifier key already exists
     * 
     * @param string $identifierKey
     * @param int $id
     * @return boolean TRUE if identifier key already exists
     */
    private function isDuplicateIdentifierKey($identifierKey, $id=0) {
        $collection = $this->getCollection(array('identifier_key' => $identifierKey, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection)?TRUE:FALSE;
    }

}