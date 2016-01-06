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
 * Class Admin_System_Email_TemplateController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_System_Email_TemplateController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/email/template');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Email Templates');
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
        $view = Fox::getView('admin/system/email/template/add');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = Fox::getModel('core/email/template');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit Email Template "' . $model->getName() . '"');
            } else {
                $view->setHeaderText('Add Email Template');
            }
        } else {
            $view->setHeaderText('Add Email Template');
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
                $model = Fox::getModel('core/email/template');
                $curDate = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                } else {
                    $data['created_date'] = $curDate;
                }
                $data['modified_date'] = $curDate;
                $model->setData($data);
                $model->save();
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
                $model = Fox::getModel('core/email/template');
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
     * Get template content action
     */
    public function getcontentAction() {
        $tempId = $this->getRequest()->getParam('tempId');
        $model = Fox::getModel('core/email/template');
        $model->load($tempId);
        if ($model->getId()) {
            echo json_encode(array('name' => $model->getName(), 'subject' => $model->getSubject(), 'content' => $model->getContent()));
        }
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('admin/system/email/template/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

}