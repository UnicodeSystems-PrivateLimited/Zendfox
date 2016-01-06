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
 * Class Admin_System_UserController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_System_UserController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/user');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Users');
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
    public function editAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/user/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('admin/user');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit User "' . $model->getUsername() . '"');
            } else {
                $view->setHeaderText('Add User');
            }
        } else {
            $view->setHeaderText('Add User');
        }
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        if (($data = $this->getRequest()->getPost())) {
            try {
                $model = Fox::getModel('admin/user');
                $password = '';
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                }
                if ($model->getId()) {
                    if (!$data['password'] && !$data['c_password']) {
                        $password = $model->getPassword();
                    }
                }
                if (!$password) {
                    if ($data['password'] && ($data['password'] == $data['c_password'])) {
                        $password = md5($data['password']);
                    } else {
                        throw new Exception('Confirm password must match password.');
                    }
                }
                $data['password'] = $password;
                $model->setData($data);
                $model->save();
                Fox::getHelper('core/message')->setInfo('"' . $model->getUsername() . '" was successfully saved.');
                if ($this->getRequest()->getParam('back')) {
                    $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->addError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $model = Fox::getModel('admin/user');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getUsername() . '" was successfully deleted.');
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
    public function ajaxTableAction() {
        $view = Fox::getView('admin/system/user/table');
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
            $model = Fox::getModel('admin/user');
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

    /**
     * Bulk status update action
     */
    public function groupStatusAction() {
        try {
            $model = Fox::getModel('admin/user');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            $dependentParams = $this->getRequest()->getParam('dependents', array());
            if (isset($dependentParams['status'])) {
                if ($totalIds) {
                    $model->update(array('status' => $dependentParams['status']), $model->getPrimaryField() . ' IN (' . implode(',', $ids) . ')');
                }
                Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' record(s) successfully updated.');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}