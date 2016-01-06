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
 * Class Fox_Eav_Model_Attribute_Group
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_Model_Attribute_Group extends Uni_Core_Model {

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'eav_attribute_group';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get assigned attribute groups in the specified set
     * 
     * @param int $attributeSetId
     * @return Uni_Core_Model_Collection
     */
    public function getAssignedGroups($attributeSetId) {
        return $this->getCollection('attribute_set_id=' . $attributeSetId, '*', 'sort_order ASC');
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if attribute group name already exists
     */
    protected function _preSave(array $data) {
        if ($this->isGroupDuplicate($data['attribute_group_name'], $data['attribute_set_id'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['attribute_group_name'] . "' group already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the attribute group name already exists
     * 
     * @param string $name
     * @param int $attributeSetId
     * @param int $id
     * @return boolean 
     */
    private function isGroupDuplicate($name, $attributeSetId, $id=0) {
        $collection = $this->getCollection(array('attribute_set_id' => $attributeSetId,'attribute_group_name'=>$name, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection) ? TRUE : FALSE;
    }

}