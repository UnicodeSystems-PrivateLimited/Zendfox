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
 * Class Admin_Eav_SetController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_Eav_SetController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('eav/admin/set');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Manage Attribute Sets');
        $this->renderLayout();
    }

    /**
     * Add action
     */
    public function addAction() {
        $this->loadLayout();
        $view = Fox::getView('eav/admin/set/add');
        if ($view) {
            $this->_addContent($view);
            $id = $this->getRequest()->getParam('id');
            $model = Fox::getModel('eav/set');
            $view->setHeaderText('Add Attribute Set');
        }
        $this->renderLayout();
    }

    /**
     * Edit action
     */
    public function editAction() {
        if (!$this->getRequest()->getParam('id')) {
            $this->sendRedirect('*/*/add');
        } else {
            $model = Fox::getModel('eav/set');
            $model->load($this->getRequest()->getParam('id'));
            if (!$model->getId()) {
                $this->sendRedirect('*/*/add');
            }
            $this->loadLayout();
            $view = Fox::getView('eav/admin/set/edit');
            if ($view) {
                $this->_addContent($view);
                $view->setHeaderText('Edit Attribute Set "' . $model->getAttributeSetName() . '"');
            }
            $this->renderLayout();
        }
    }

    /**
     * Save action
     */
    public function saveAction() {
        $model = Fox::getModel('eav/set');
        $db = $model->getAdapter();
        try {
            $db->beginTransaction();
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                $model->setData($data);
                $model->save();
                $basedOnId = $data['attribute_set_based_on'];
                if ($basedOnId) {
                    $groupModel = Fox::getModel('eav/attribute/group');
                    $entityAttributeModel = Fox::getModel('eav/entity');
                    $groupCollection = $groupModel->getCollection("attribute_set_id=$basedOnId");
                    foreach ($groupCollection as $group) {
                        $groupModel = Fox::getModel('eav/attribute/group');
                        $groupModel->setAttributeSetId($model->getId());
                        $groupModel->setAttributeGroupName($group['attribute_group_name']);
                        $groupModel->setSortOrder($group['sort_order']);
                        $groupModel->save();
                        $attributeCollection = $entityAttributeModel->getCollection("attribute_group_id=" . $group['id']);
                        foreach ($attributeCollection as $attribute) {
                            $entityAttributeModel = Fox::getModel('eav/entity');
                            $entityAttributeModel->setAttributeSetId($model->getId());
                            $entityAttributeModel->setAttributeId($attribute['attribute_id']);
                            $entityAttributeModel->setAttributeGroupId($groupModel->getId());
                            $entityAttributeModel->setSortOrder($attribute['sort_order']);
                            $entityAttributeModel->save();
                            $entityAttributeModel->unsetData();
                        }
                        $groupModel->unsetData();
                    }
                }
                $db->commit();
                Fox::getHelper('core/message')->setInfo('"' . $model->getAttributeSetName() . '" was successfully saved.');
                if ($this->getRequest()->getParam('back')) {
                    $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
            }
        } catch (Exception $e) {
            Fox::getModel('core/session')->setFormData($data);
            Fox::getHelper('core/message')->setError($e->getMessage());
            $db->rollback();
            $this->sendRedirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Update action
     */
    public function updateAction() {
        $result = array();
        $redirectUrl = '';
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $model = Fox::getModel('eav/set');
            $groupModel = Fox::getModel('eav/attribute/group');
            $entityModel = Fox::getModel('eav/entity');
            $db = $model->getAdapter();
            try {
                $db->beginTransaction();
                $model->load($data['id']);
                $model->setAttributeSetName($data['attribute_set_name']);
                $treeDataArr = json_decode($data['attribute_set_tree'], true);
                $unassignedTreeData = json_decode($data['unassigned_attribute_set_tree'], true);
                $groups = array();
                $groupPrevs = explode(',', $data['groups_prev']);
                $unAssignedAttributePrevs = explode(',', $data['unassigned_attributes_prev']);
                $unAssignedAttributes = array();
                if (is_array($unassignedTreeData)) {
                    $unassignedAttrData = array();
                    if (isset($unassignedTreeData[0]['children'])) {
                        $unassignedAttrData = $unassignedTreeData[0]['children'];
                    }
                    foreach ($unassignedAttrData as $unAssignedAttribute) {
                        if ($unAssignedAttribute['attr']['rel'] == Fox_Eav_View_Admin_Set_Edit_Tree::NODE_TYPE_ATTRIBUTE_USER) {
                            $unAssignedAttributes[] = $unAssignedAttribute['attr']['id'];
                        }
                    }
                }
                $i = 0;
                $model->save();
                if (is_array($treeDataArr)) {
                    foreach ($treeDataArr as $treeData) {
                        $j = 0;
                        if (isset($treeData['attr']['id']) && $treeData['attr']['id'] > 0) {
                            $groups[] = $treeData['attr']['id'];
                            $groupModel->load($treeData['attr']['id']);
                        }
                        $groupModel->setAttributeSetId($data['id']);
                        $groupModel->setAttributeGroupName($treeData['data']);
                        $groupModel->setSortOrder($i);
                        $groupModel->save();
                        if (isset($treeData['children']) && count($treeData['children'])) {
                            $attributes = $treeData['children'];
                            foreach ($attributes as $attribute) {
                                $entityModel->load(array('attribute_set_id' => $data['id'], 'attribute_id' => $attribute['attr']['id']));
                                $entityModel->setAttributeSetId($data['id']);
                                $entityModel->setAttributeGroupId($groupModel->getId());
                                $entityModel->setAttributeId($attribute['attr']['id']);
                                $entityModel->setSortOrder($j);
                                $entityModel->save();
                                $entityModel->unsetData();
                                $j++;
                            }
                        }
                        $groupModel->unsetData();
                        $i++;
                    }
                }
                $delAttribs = implode(',', array_diff($unAssignedAttributes, $unAssignedAttributePrevs));
                $delGroups = implode(',', array_diff($groupPrevs, $groups));
                if ($delAttribs) {
                    $entityModel->delete("attribute_set_id=" . $data['id'] . " AND attribute_id IN ($delAttribs)");
                }
                if ($delGroups) {
                    $groupModel->delete("id IN ($delGroups)");
                }
                $db->commit();
                Fox::getHelper('core/message')->unsetErrorMessages();
                Fox::getHelper('core/message')->setInfo('"' . $model->getAttributeSetName() . '" was successfully saved.');
                if ($this->getRequest()->getParam('back')) {
                    $redirectUrl = Fox::getUrl('*/*/edit', array('id' => $model->getId()));
                } else {
                    $redirectUrl = Fox::getUrl('*/*/');
                }
                $result = array('success' => true, 'redirectUrl' => $redirectUrl);
            } catch (Exception $e) {
                $db->rollback();
                $result = array('error' => $e->getMessage());
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        } else {
            $error = 'Error occured while saving attribute set.';
            $result = array('error' => $error);
            Fox::getHelper('core/message')->setError($error);
        }
        echo Zend_Json_Encoder::encode($result);
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $model = Fox::getModel('eav/set');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getAttributeSetName() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $id));
            }
            $this->sendRedirect('*/*/');
        }
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('eav/admin/set/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

    /**
     * Get entity attributes action
     */
    public function getattributesAction() {
        $html = '';
        if (($entityTypeId = $this->getRequest()->getParam('entity_type'))) {
            $model = Fox::getModel('eav/set');
            $collection = $model->getCollection("entity_type_id=$entityTypeId");
            foreach ($collection as $record) {
                $html.='<option value="' . $record['id'] . '">' . $record['attribute_set_name'] . '</option>';
            }
        } else {
            $html = '<option value=""></option>';
        }
        echo $html;
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('eav/set');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            if ($totalIds) {
                $model->delete($model->getPrimaryField() . ' IN (' . implode(',', $ids) . ')');
            }
            Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' record(s) successfully deleted.');
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}