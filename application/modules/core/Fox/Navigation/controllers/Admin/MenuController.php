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
 * Class Navigation_Admin_MenuController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Navigation_Admin_MenuController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('navigation/admin/menu');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Manage Menu Items');
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
        $view = Fox::getView('navigation/admin/menu/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('navigation/menu');
            $model->load($id);
            if ($model->getId()) {
                $model->setMenuGroup(explode(',', $model->getMenuGroup()));
                $view->setHeaderText('Edit Menu Item "' . $model->getTitle() . '"');
            } else {
                $view->setHeaderText('Add Menu Item');
            }
        } else {
            $view->setHeaderText('Add Menu Item');
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
                $model = Fox::getModel('navigation/menu');
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                }
                $data['menu_group'] = isset($data['menu_group']) ? implode(',', $data['menu_group']) : '';
                $model->setData($data);
                $model->save();
                Fox::getHelper('core/message')->setInfo('"' . $data['title'] . '"' . ' was successfully saved.');
                if ($this->getRequest()->getParam('back')) {
                    $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->addError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->sendRedirect('*/*');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $model = Fox::getModel('navigation/menu');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getTitle() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->addError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('navigation/admin/menu/table');
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
            $model = Fox::getModel('navigation/menu');
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
            $model = Fox::getModel('navigation/menu');
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