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
 * Class Contact_Admin_ContactController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Contact
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Contact_Admin_ContactController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('contact/admin/contact');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Contact Requests');
        $this->renderLayout();
    }

    /**
     * View action
     */
    public function viewAction() {
        $this->loadLayout();
        $view = Fox::getView('contact/admin/contact/view');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id');
        $model = Fox::getModel('contact/contact');
        $model->load($id);
        if ($model->getId()) {
            if ($model->getStatus() == Fox_Contact_Model_Contact::STATUS_UNREAD) {
                $model->setStatus(Fox_Contact_Model_Contact::STATUS_READ);
            }
            $model->save();
            $view->setHeaderText('View Request From "' . $model->getName() . '"');
        } else {
            $this->sendRedirect('*/*/');
        }
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            $model = Fox::getModel('contact/contact');
            $model->getAdapter()->beginTransaction();
            try {
                $data = $this->getRequest()->getPost();
                if (($id = $this->getRequest()->getParam('id'))) {
                    $model->load($id);
                    if ($model->getId()) {
                        $model->reply($data['reply_message']);
                        $model->getAdapter()->commit();
                        Fox::getHelper('core/message')->setInfo('Reply to "' . $model->getName() . '" was successfully sent.');
                    }
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->addError($e->getMessage());
                Fox::getModel('core/session')->setFormData($data);
                $model->getAdapter()->rollback();
                $this->sendRedirect('*/*/reply', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('contact/admin/contact/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }
    
    /**
     * Export action
     * 
     * @return void
     */
    public function exportAction() {
        $view = Fox::getView('contact/admin/contact/table');
        if ($view) {
            $view->setDefaultRecordCount(0);
            $view->renderView();
            $type = $this->getRequest()->getParam('exportType');
            if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_CSV) {
                $view->exportToCSV('contact.csv');
            } else if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_XML) {
                $view->exportToXML('contact.xml');
            }
        }
        return;
    }

    /**
     * Reply action
     */
    public function replyAction() {
        $this->loadLayout();
        $id = $this->getRequest()->getParam('id');
        $model = Fox::getModel('contact/contact');
        $model->load($id);
        if ($model->getId()) {
            $view = Fox::getView('contact/admin/contact/reply');
            if ($view) {
                $this->_addContent($view);
            }
            $view->setHeaderText('Send Reply to "' . $model->getName() . '"');
        } else {
            $this->sendRedirect('*/*/');
        }
        $this->renderLayout();
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $model = Fox::getModel('contact/contact');
                $model = Fox::getModel('contact/contact');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getName() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
                $this->sendRedirect('*/*/reply', array('id' => $id));
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('contact/contact');
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
            $model = Fox::getModel('contact/contact');
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