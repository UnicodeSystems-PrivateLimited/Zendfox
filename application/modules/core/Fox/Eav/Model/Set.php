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
 * Class Fox_Eav_Model_Set
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_Model_Set extends Uni_Core_Model {

    /**
     * Eav entity type table name
     * 
     * @var string
     */
    protected $_entityType = 'eav_entity_type';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'eav_attribute_set';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if attribute set name already exists
     */
    protected function _preSave(array $data) {
        if ($this->isSetDuplicate($data['attribute_set_name'], $data['entity_type_id'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['attribute_set_name'] . "' already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the attribute set name already exists
     * 
     * @param string $name attribute set name
     * @param int $entityTypeId entity type id
     * @param int $id attribute set id
     * @return boolean TRUE if attribute set name already exists
     */
    private function isSetDuplicate($name, $entityTypeId, $id=0) {
        $collection = $this->getCollection("attribute_set_name='$name' AND entity_type_id=$entityTypeId AND id!=$id");
        return count($collection) ? TRUE : FALSE;
    }

    /**
     * Get attribute set for entity
     * 
     * @param string $entityTypeCode
     * @return array 
     */
    public function getAttributeSetByEntity($entityTypeCode) {
        $select = $this->getAdapter()->select()->from(array('a' => $this->_name))
                ->join(array('e' => Fox::getTableName($this->_entityType)), 'a.entity_type_id=e.id', FALSE)
                ->where('e.entity_type_code=?', $entityTypeCode);
        return $this->getAdapter()->fetchRow($select);
    }

}