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
 * Class Admin_System_RoleController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_System_RoleController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/role');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Roles');
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
        $view = Fox::getView('admin/system/role/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('admin/role');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit Role "' . $model->getName() . '"');
            } else {
                $view->setHeaderText('Add Role');
            }
        } else {
            $view->setHeaderText('Add Role');
        }
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            try {
                $model = Fox::getModel('admin/role');
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                }
                if ($data['resource_access'] == Fox_Admin_Model_Role::ROLE_ACCESS_ALL) {
                    $data['resource_ids'] = Fox_Admin_Model_Role::ROLE_ACCESS_ALL;
                } else if ($data['resource_access'] == Fox_Admin_Model_Role::ROLE_ACCESS_CUSTOM && empty($data['custom_ids'])) {
                    $data['resource_ids'] = Fox_Admin_Model_Role::ROLE_DENY_ALL;
                } else {
                    $resources = json_decode($data['custom_ids'], TRUE);
                    $resourceIds = array();
                    while (count($resources) > 0) {
                        $resource = array_shift($resources);
                        if (isset($resource['children'])) {
                            foreach ($resource['children'] as $child) {
                                $resources[] = $child;
                            }
                        }
                        if ($resource['attr']['rel'] == 'resource') {
                            $resourceIds[] = $resource['attr']['id'];
                        } else {
                            if ((!isset($resource['children']) || count($resource['children']) == 0) && !in_array($resource['attr']['id'], $resourceIds)) {
                                $resourceIds[] = $resource['attr']['id'];
                            }
                        }
                    }
                    $data['resource_ids'] = implode(',', $resourceIds);
                }
                $model->setData($data);
                $model->save();
                if ($model->getId() == Fox::getModel('admin/session')->getRole()) {
                    Fox::getHelper('admin/acl')->loadResources();
                }
                Fox::getHelper('core/message')->setInfo('"' . $model->getName() . '" was successfully saved.');
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
                $model = Fox::getModel('admin/role');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getName() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->addError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $id));
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Table date action
     */
    public function ajaxTableAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/role/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

}