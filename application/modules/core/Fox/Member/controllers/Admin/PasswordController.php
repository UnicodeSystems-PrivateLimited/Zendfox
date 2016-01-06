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
 * Class Member_Admin_PasswordController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Member_Admin_PasswordController extends Fox_Admin_Controller_Action {

    /**
     * Save action
     */
    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            try {
                $model = Fox::getModel('member/member');
                $model->load($data['email_id'], 'email_id');
                if ($model->getId()) {
                    if ($data['password'] == $data['c_password']) {
                        $model->setPassword(md5($data['password']));
                        $model->save();
                        $model->sendPasswordChangedMail($data['password']);
                        Fox::getHelper('core/message')->setInfo('Password was successfully changed.');
                    } else {
                        throw new Exception('Password must match confirm password.');
                    }
                } else {
                    throw new Exception('Email Id was not found.');
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->sendRedirect('*/*/change-password');
    }

    /**
     * Change password page
     */
    public function changePasswordAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            $model = Fox::getModel('member/member');
            $model->load($id);
            if ($model->getId()) {
                $data['email_id'] = $model->getEmailId();
                Fox::getModel('core/session')->setFormData($data);
            }
        }
        $this->loadLayout();
        $view = Fox::getView('member/admin/member/password');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Change Member Password');
        $this->renderLayout();
    }

}