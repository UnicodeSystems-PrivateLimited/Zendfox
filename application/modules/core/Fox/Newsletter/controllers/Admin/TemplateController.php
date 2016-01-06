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
 * Class Newsletter_Admin_TemplateController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Newsletter_Admin_TemplateController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('newsletter/admin/template');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Newsletter Templates');
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
        $view = Fox::getView('newsletter/admin/template/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('newsletter/template');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit Newsletter Template "' . $model->getName() . '"');
            } else {
                $view->setHeaderText('Add Newsletter Template');
            }
        } else {
            $view->setHeaderText('Add Newsletter Template');
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
                $model = Fox::getModel('newsletter/template');
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                } else {
                    $data['create_date'] = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
                }
                $data['update_date'] = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
                $model->setData($data);
                $model->save();
                Fox::getHelper('core/message')->addInfo('Template was successfully saved.');
                if ($this->getRequest()->getParam('back')) {
                    $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->addError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
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
                $model = Fox::getModel('newsletter/template');
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
     * Preview action
     */
    public function previewAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('newsletter/admin/template/table');
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
            $model = Fox::getModel('newsletter/template');
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
     * Send newsletter action
     */
    public function sendAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = Fox::getModel('newsletter/template');
                $model->load($id);
                if ($model->getId()) {
                    $model->sendNewsletter();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getName() . '" was successfully sent.');
                } else {
                    Fox::getHelper('core/message')->setError('Newsletter template was not found.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->sendRedirect('*/*/');
    }

}