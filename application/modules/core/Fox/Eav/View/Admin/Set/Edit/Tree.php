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
 * Class Fox_Eav_View_Admin_Set_Edit_Tree
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Set_Edit_Tree extends Uni_Core_Admin_View {

    /**
     * @var string
     */
    protected $id;

    const NODE_TYPE_GROUP='group';
    const NODE_TYPE_ATTRIBUTE_SYSTEM='attrib_system';
    const NODE_TYPE_ATTRIBUTE_USER='attrib_user';
    const STATE_OPENED='open';
    const STATE_CLOSED='closed';

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('eav/set/tree');
    }

    /**
     * Get view id
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set view id
     * 
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get attribute set model object
     * 
     * @return Fox_Eav_Model_Set
     */
    public function getAttributeSet() {
        $attributeSetModel = Fox::getModel('eav/set');
        return $attributeSetModel->load($this->getRequest()->getParam('id'));
    }

    /**
     * Get unassigned view id
     * 
     * @return string
     */
    public function getUnassignedId() {
        return get_class($this) . '-unassigned';
    }

    /**
     * Get attribute data
     * 
     * @return string Returns data as json string
     */
    public function getTreeData() {
        $attributeSetId = $this->getRequest()->getParam('id');
        $groupModel = Fox::getModel('eav/attribute/group');
        $attributeModel = Fox::getModel('eav/attribute');
        $assignedGroups = $groupModel->getAssignedGroups($attributeSetId);
        $setData = array();
        foreach ($assignedGroups as $assignedGroup) {
            $groupDetail = array(
                'attr' => array(
                    'id' => $assignedGroup['id'],
                    'rel' => self::NODE_TYPE_GROUP
                ),
                'data' => $assignedGroup['attribute_group_name'],
                'state' => self::STATE_OPENED
            );
            $attributes = $attributeModel->getGroupAttributes($assignedGroup['id']);
            foreach ($attributes as $attribute) {
                $groupDetail['children'][] = array(
                    'attr' => array(
                        'id' => $attribute['attribute_id'],
                        'rel' => ($attribute['is_user_defined'] == Fox_Eav_Model_Attribute::ATTRIBUTE_USER_DEFINED_YES) ? self::NODE_TYPE_ATTRIBUTE_USER : self::NODE_TYPE_ATTRIBUTE_SYSTEM
                    ),
                    'data' => $attribute['attribute_code']
                );
            }
            $setData[] = $groupDetail;
        }
        return Zend_Json_Encoder::encode($setData);
    }

    /**
     * Get unassigned attribute data
     * 
     * @param string $entityTypeId
     * @return string Returns data as json string
     */
    public function getUnassignedAttributeData($entityTypeId) {
        $attributeSetId = $this->getRequest()->getParam('id');
        $attributeModel = Fox::getModel('eav/attribute');
        $setData = array();
        $emptyGroupDetail = array(
            'attr' => array(
                'id' => '',
                'rel' => self::NODE_TYPE_GROUP
            ),
            'data' => '',
            'state' => self::STATE_OPENED
        );
        $unassignedAttributes = $attributeModel->getUnassignedAttributes($attributeSetId, $entityTypeId);
        foreach ($unassignedAttributes as $unassignedAttribute) {
            $emptyGroupDetail['children'][] = array(
                'attr' => array(
                    'id' => $unassignedAttribute['id'],
                    'rel' => ($unassignedAttribute['is_user_defined'] == Fox_Eav_Model_Attribute::ATTRIBUTE_USER_DEFINED_YES) ? self::NODE_TYPE_ATTRIBUTE_USER : self::NODE_TYPE_ATTRIBUTE_SYSTEM
                ),
                'data' => $unassignedAttribute['attribute_code']
            );
        }
        $setData[] = $emptyGroupDetail;
        return Zend_Json_Encoder::encode($setData);
    }

    /**
     * Get attribute group ids
     * 
     * @return string Returns group ids as comma-separated string
     */
    public function getGroupIds() {
        $attributeSetId = $this->getRequest()->getParam('id');
        $groupModel = Fox::getModel('eav/attribute/group');
        $assignedGroups = $groupModel->getAssignedGroups($attributeSetId);
        $groupIds = array();
        foreach ($assignedGroups as $assignedGroup) {
            $groupIds[] = $assignedGroup['id'];
        }
        return implode(',', $groupIds);
    }

    /**
     * Get unassigned attribute ids
     * 
     * @param string $entityTypeId
     * @return string Returns unassigned attribute ids as comma-separated string
     */
    public function getUnassignedAttributeIds($entityTypeId) {
        $attributeSetId = $this->getRequest()->getParam('id');
        $attributeModel = Fox::getModel('eav/attribute');
        $setData = array();
        $unassignedAttributes = $attributeModel->getUnassignedAttributes($attributeSetId, $entityTypeId);
        foreach ($unassignedAttributes as $unassignedAttribute) {
            $setData[] = $unassignedAttribute['id'];
        }
        return implode(',', $setData);
    }

    /**
     * Get tree data action url
     * 
     * @return string
     */
    public function getTreeDataUrl() {
        return Fox::getUrl('*/*/ajax-tree-data');
    }

    /**
     * Get update action url
     * 
     * @return string
     */
    public function getUpdateUrl() {
        return Fox::getUrl('*/*/update');
    }

    /**
     * Prepare tree
     */
    public function _prepareTree() {
        if (NULL == $this->id) {
            $this->id = get_class($this);
        }
    }

    /**
     * Render contents
     * 
     * @return string
     */
    public function renderView() {
        $this->_prepareTree();
        return parent::renderView();
    }

}
