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
 * Class Fox_Eav_Model_Attribute
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_Model_Attribute extends Uni_Core_Model {
    const ATTRIBUTE_REQUIRED_NO=0;
    const ATTRIBUTE_REQUIRED_YES=1;
    const ATTRIBUTE_UNIQUE_NO=0;
    const ATTRIBUTE_UNIQUE_YES=1;
    const ATTRIBUTE_EDITABLE_NO=0;
    const ATTRIBUTE_EDITABLE_YES=1;
    const ATTRIBUTE_EDITABLE_BACKEND_NO=0;
    const ATTRIBUTE_EDITABLE_BACKEND_YES=1;
    const ATTRIBUTE_VISIBILE_ON_FRONTEND_NO=0;
    const ATTRIBUTE_VISIBILE_ON_FRONTEND_YES=1;
    const ATTRIBUTE_USER_DEFINED_NO=0;
    const ATTRIBUTE_USER_DEFINED_YES=1;
    const ATTRIBUTE_TYPE_SELECT='select';
    const ATTRIBUTE_TYPE_MULTISELECT='multiselect';
    const ATTRIBUTE_TYPE_RADIO='radio';
    const ATTRIBUTE_TYPE_DATE='date';
    const ATTRIBUTE_TYPE_PRICE='price';
    const ATTRIBUTE_TYPE_TEXTFIELD='text';
    const ATTRIBUTE_TYPE_TEXTAREA='textarea';
    const ATTRIBUTE_TYPE_BOOLEAN='boolean';
    const ATTRIBUTE_TYPE_FILE='file';
    const ATTRIBUTE_DATA_TYPE_VARCHAR='varchar';
    const ATTRIBUTE_DATA_TYPE_DATE='date';
    const ATTRIBUTE_DATA_TYPE_DECIMAL='decimal';
    const ATTRIBUTE_DATA_TYPE_TEXT='text';
    const ATTRIBUTE_DATA_TYPE_INT='int';
    const ATTRIBUTE_VALIDATION_DECIMAL='number';
    const ATTRIBUTE_VALIDATION_INT='integer';
    const ATTRIBUTE_VALIDATION_URL='url';
    const ATTRIBUTE_VALIDATION_LETTERS='alnum';
    const ATTRIBUTE_VALIDATION_EMAIL='email';
    const ATTRIBUTE_VALIDATION_IMAGE='image';
    const ATTRIBUTE_VALIDATION_DATE='date';
    /**
     * Attribute and group relation table
     * 
     * @var string 
     */
    protected $entityAttribute = 'eav_entity_attribute';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'eav_attribute';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get attribute required status options
     * 
     * @return array
     */
    public function getRequiredStatuses() {
        return array('' => '', self::ATTRIBUTE_REQUIRED_YES => 'Yes', self::ATTRIBUTE_REQUIRED_NO => 'No');
    }

    /**
     * Get attribute unique status options
     * 
     * @return array
     */
    public function getUniqueStatuses() {
        return array('' => '', self::ATTRIBUTE_UNIQUE_YES => 'Yes', self::ATTRIBUTE_UNIQUE_NO => 'No');
    }

    /**
     * Get attribute editable status options
     * 
     * @return array
     */
    public function getEditableStatuses() {
        return array('' => '', self::ATTRIBUTE_EDITABLE_YES => 'Yes', self::ATTRIBUTE_EDITABLE_NO => 'No');
    }

    /**
     * Get attribute visible status options for frontend
     * 
     * @return array
     */
    public function getFrontendVisibleStatuses() {
        return array('' => '', self::ATTRIBUTE_VISIBILE_ON_FRONTEND_YES => 'Yes', self::ATTRIBUTE_VISIBILE_ON_FRONTEND_NO => 'No');
    }

    /**
     * Get input types
     * 
     * @return array
     */
    public function getInputTypes() {
        return array('' => '',
            self::ATTRIBUTE_TYPE_BOOLEAN => 'Yes/No',
            self::ATTRIBUTE_TYPE_DATE => 'Date',
            self::ATTRIBUTE_TYPE_FILE => 'File',
            self::ATTRIBUTE_TYPE_MULTISELECT => 'Multi Select',
            self::ATTRIBUTE_TYPE_SELECT => 'Dropdown',
            self::ATTRIBUTE_TYPE_PRICE => 'Price',
            self::ATTRIBUTE_TYPE_RADIO => 'Radio',
            self::ATTRIBUTE_TYPE_TEXTAREA => 'Text Area',
            self::ATTRIBUTE_TYPE_TEXTFIELD => 'Text Field',
        );
    }

    /**
     * Get attributes for the specified group
     * 
     * @param int $groupId
     * @return array 
     */
    public function getGroupAttributes($groupId) {
        $select = $this->getAdapter()->select()->from(array('a' => $this->_name))->join(array('e' => Fox::getTableName($this->entityAttribute)), 'a.id=e.attribute_id')->where("e.attribute_group_id=$groupId")->order('e.sort_order ASC');
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * Get unused attributes
     * 
     * @param int $attributeSetId
     * @param int $entityTypeId
     * @return Uni_Core_Model_Collection 
     */
    public function getUnassignedAttributes($attributeSetId, $entityTypeId) {
        return $this->getCollection('entity_type_id=' . $entityTypeId . ' AND id NOT IN(SELECT e.attribute_id FROM ' . Fox::getTableName($this->entityAttribute) . ' e WHERE e.attribute_set_id=' . $attributeSetId . ')');
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if attribute code already exists
     */
    protected function _preSave(array $data) {
        if ($this->isAttributeDuplicate($data['attribute_code'], (isset($data['id']) && $data['id']) ? $data['id'] : 0)) {
            throw new Exception("'" . $data['attribute_code'] . "' already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the attribute code already exists
     * 
     * @param string $name attribute code
     * @param int $id attribute id
     * @return boolean TRUE if attribute code already exists
     */
    private function isAttributeDuplicate($attrCode, $id=0) {
        $collection = $this->getCollection(array('attribute_code' => $attrCode, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection) ? TRUE : FALSE;
    }

}
