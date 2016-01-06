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
 * Class Fox_Navigation_Model_Menugroup
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_Model_Menugroup extends Uni_Core_Model {
    const STATUS_ENABLED=1;
    const STATUS_DISABLED=0;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'navigation_menu_group';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get status options
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

    /**
     * Get menu groups
     * 
     * @return array
     */
    public function getMenuGroupOptions() {
        $collection = $this->getCollection();
        $groups = array();
        foreach ($collection as $group) {
            $groups[$group['id']] = $group['name'];
        }
        return $groups;
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if group key already exists
     */
    protected function _preSave(array $data) {
        $id = (isset($data['id']) && $data['id']) ? $data['id'] : 0;
        $collection = $this->getCollection(array('key' => $data['key'], 'id' => array('operator' => '!=', 'value' => $id)));
        if (count($collection)) {
            throw new Exception('Key already exists.');
        }
        return $data;
    }

}