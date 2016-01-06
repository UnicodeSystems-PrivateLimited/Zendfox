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
 * Class Member_Admin_MemberController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Member_Admin_MemberController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('member/admin/member');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Members');
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
        $view = Fox::getView('member/admin/member/edit');
        if ($view) {
            $this->_addContent($view);
        }
        $id = $this->getRequest()->getParam('id', FALSE);
        if ($id) {
            $model = Fox::getModel('member/member');
            $model->load($id);
            if ($model->getId()) {
                $view->setHeaderText('Edit Member "' . $model->getFirstName() . '"');
            } else {
                $view->setHeaderText('Add Member');
            }
        } else {
            $view->setHeaderText('Add Member');
        }
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        if (($data = $this->getRequest()->getPost())) {
            try {
                $model = Fox::getModel('member/member');
                if (isset($data['id']) && $data['id']) {
                    $model->load($data['id']);
                    $model->setData($data);
                    $model->save();
                    Fox::getHelper('core/message')->setInfo('Member was successfully updated.');
                } else {
                    $model->createMember($data);
                    Fox::getHelper('core/message')->setInfo("New member was successfully added.");
                }
                if ($this->getRequest()->getParam('back')) {
                    $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->setError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
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
                $model = Fox::getModel('member/member');
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('Member was successfully deleted.');
                    $this->sendRedirect('*/*/');
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->setError($e->getMessage());
                $this->sendRedirect('*/*/edit', array('id' => $model->getId()));
            }
        }
        $this->sendRedirect('*/*');
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('member/admin/member/table');
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
        $view = Fox::getView('member/admin/member/table');
        if ($view) {
            $view->setDefaultRecordCount(0);
            $view->renderView();
            $type = $this->getRequest()->getParam('exportType');
            if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_CSV) {
                $view->exportToCSV('member.csv');
            } else if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_XML) {
                $view->exportToXML('member.xml');
            }
        }
        return;
    }

    /**
     * Member login from admin
     * 
     * @return void
     */
    public function memberLoginAction() {
        $id = $this->getRequest()->getParam('id', FALSE);
        if ($id) {
            $model = Fox::getModel('member/member');
            $model->load($id);
            if ($model->getId()) {
                Fox::getModel('member/session')->setLoginData($model);
                $this->sendRedirect('member');
                return;
            } else {
                Fox::getHelper('core/message')->setError('Member was not found.');
            }
        }
        $this->sendRedirect('*/*');
    }

    /**
     * Bulk status update action
     */
    public function groupStatusAction() {
        try {
            $model = Fox::getModel('member/member');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            $dependentParams = $this->getRequest()->getParam('dependents', array());
            if (isset($dependentParams['status'])) {
                if ($totalIds) {
                    foreach ($ids as $id) {
                        $model->load($id);
                        if ($model->getId()) {
                            if ($dependentParams['status'] != Fox_Member_Model_Status::STATUS_BLOCKED && $model->getStatus() == Fox_Member_Model_Status::STATUS_NOT_CONFIRM) {
                                $model->sendConfirmedMail(array('name' => $model->getFirstName()));
                            }
                            $model->setStatus($dependentParams['status']);
                            $model->save();
                        }
                        $model->unsetData();
                    }
                }
                Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' record(s) successfully updated');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('member/member');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            $count = 0;
            foreach ($ids as $id) {
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    $model->unsetData();
                    $count++;
                }
            }
            Fox::getHelper('core/message')->setInfo('Total ' . $count . ' record(s) successfully deleted.');
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}