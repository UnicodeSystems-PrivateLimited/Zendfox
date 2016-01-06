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
 * Class Admin_Eav_AttributeController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_Eav_AttributeController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('eav/admin/attribute');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Manage Attributes');
        $this->renderLayout();
    }

    /**
     * Add action
     */
    public function addAction() {
        $this->sendForward('*/*/edit');
    }

    /**
     * Edit action
     */
    function editAction() {
        $this->loadLayout();
        $view = Fox::getView('eav/admin/attribute/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('eav/attribute');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit Attribute "' . $model->getFrontendLabel() . '"');
            } else {
                $view->setHeaderText('Add Attribute');
            }
        } else {
            $view->setHeaderText('Add Attribute');
        }
        $this->renderLayout();
    }

    /**
     * Save action
     */
    function saveAction() {
        $result = array();
        $redirectUrl = '';
        $id = '';
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $model = Fox::getModel('eav/attribute');
            $entityModel = Fox::getModel('eav/entity/type');
            $db = $model->getAdapter();
            try {
                $db->beginTransaction();
                $attrData = isset($data['attr']) ? $data['attr'] : array();
                $validationClass = '';
                if (isset($attrData['attribute_code']) && strpos($attrData['attribute_code'], ' ')) {
                    throw new Exception('Attribute code should not have spaces.');
                }
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                }
                if ($model->getId()) {
                    $attrData['input_type'] = $model->getInputType();
                    $attrData['data_type'] = $model->getDataType();
                } else {
                    $attrData['data_type'] = Fox::getHelper('eav/attribute')->getDataType($attrData['input_type']);
                }
                if (!isset($attrData['is_user_defined'])) {
                    $attrData['is_user_defined'] = Fox_Eav_Model_Attribute::ATTRIBUTE_USER_DEFINED_YES;
                }
                $model->setData($attrData);
                if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_TEXTFIELD || $attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_PRICE) {
                    $model->setDefaultValue($attrData['default_value_textfield']);
                } else if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_DATE) {
                    if ($attrData['default_value_date'] && strtotime($attrData['default_value_date']) <= 0) {
                        throw new Exception('Invalid default date.');
                    }
                    $validationClass = Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_DATE;
                    $model->setDefaultValue($attrData['default_value_date']);
                } else if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_TEXTAREA) {
                    $model->setDefaultValue($attrData['default_value_textarea']);
                } else if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_BOOLEAN) {
                    $model->setDefaultValue($attrData['default_value_boolean']);
                }
                if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_TEXTFIELD) {
                    $validationClass = $attrData['validation_others'];
                } else if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_FILE) {
                    $validationClass = $attrData['validation_image'];
                    $attrData['is_unique'] = Fox_Eav_Model_Attribute::ATTRIBUTE_UNIQUE_NO;
                }
                $model->setValidation($validationClass);
                $model->save();
                if ($attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_SELECT || $attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_MULTISELECT || $attrData['input_type'] == Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_RADIO) {
                    $optData = isset($data['opt']) ? $data['opt'] : array();
                    $optIds = isset($optData['option_id']) ? $optData['option_id'] : array();
                    $optLabel = isset($optData['option_label']) ? $optData['option_label'] : array();
                    $optValue = isset($optData['option_value']) ? $optData['option_value'] : array();
                    $optPosition = isset($optData['option_position']) ? $optData['option_position'] : array();
                    $optionModel = Fox::getModel('eav/attribute/option');
                    foreach ($optValue as $key => $value) {
                        if (isset($optIds[$key])) {
                            $optionModel->load($optIds[$key]);
                        }
                        $optionModel->setAttributeId($model->getId());
                        $optionModel->setLabel($optLabel[$key]);
                        $optionModel->setValue($optValue[$key]);
                        $optionModel->setSortOrder($optPosition[$key]);
                        $optionModel->setIsDefault(isset($optData['option_is_default']) && ($optData['option_is_default'] == $key) ? Fox_Eav_Model_Attribute_Option::OPTION_IS_DEFAULT_YES : Fox_Eav_Model_Attribute_Option::OPTION_IS_DEFAULT_NO);
                        $optionModel->save();
                        $optionModel->unsetData();
                    }
                    if ($id && $data['option_delete'] == 1) {
                        $optionModel->delete('id IN (' . $data['option_delete_ids'] . ')');
                    }
                }
                $db->commit();
                Fox::getHelper('core/message')->unsetErrorMessages();
                Fox::getHelper('core/message')->setInfo('"' . $model->getFrontendLabel() . '" was successfully saved.');
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
            $result = array('error' => 'Attribute could not be saved.');
            Fox::getHelper('core/message')->setError('Attribute could not be saved.');
        }
        echo Zend_Json_Encoder::encode($result);
    }

    /**
     * Delete action
     */
    function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $model = Fox::getModel('eav/attribute');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getFrontendLabel() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $id));
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Table data action
     */
    function ajaxTableAction() {
        $view = Fox::getView('eav/admin/attribute/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('eav/attribute');
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