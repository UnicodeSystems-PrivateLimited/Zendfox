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
 * Class Admin_IndexController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 * 
 */
class Admin_IndexController extends Fox_Admin_Controller_Action {

    /**
     * Index action 
     */
    public function indexAction() {
        if (Fox::getModel('admin/session')->getLoginData()) {
            $this->sendRedirect('*/dashboard/');
        }
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $username = $data['username'];
                $password = $data['password'];
                $adminUserModel = Fox::getModel('admin/user');
                $user = $adminUserModel->load($username, 'username');
                if ($user && $user->getPassword() == md5($password)) {
                    if ($user->getStatus() == Fox_Admin_Model_User::USER_STATUS_ENABLED) {
                        Fox::getModel('admin/session')->setLoginData($user);                        
                        Fox::getHelper('admin/acl')->loadResources();
                        $this->sendRedirect('*/dashboard/');
                    } else {
                        Fox::getModel('admin/session')->setLoginError(TRUE);
                        throw new Exception('User account is blocked.');
                    }
                } else {
                    Fox::getModel('admin/session')->setLoginError(TRUE);
                    throw new Exception('Invalid username or password.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Forget password action
     * 
     * Sends an email with link to user which redirects to change password interface
     */
    public function forgetPasswordAction() {
        if (Fox::getModel('admin/session')->getLoginData()) {
            $this->sendRedirect('*/dashboard/');
        }
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $model = Fox::getModel('admin/user');
                $model->load($data['username'], 'username');
                if ($model->getId()) {
                    $code = ( strtolower(substr(md5(time() * mt_rand()), 0, 20)));
                    $modelSetCode = Fox::getModel('admin/forgetPassword');
                    $modelSetCode->load($model->getId(), 'user_id');
                    $modelSetCode->setUserId($model->getId());
                    $modelSetCode->setCode($code);
                    $modelSetCode->setCreatedOn(Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s'));
                    $modelSetCode->save();
                    Fox::getHelper('core/message')->setInfo('Change Password link has been sent to your email id.');
                    try {
                        $modelTemplate = Fox::getModel('core/email/template');
                        $sender = Fox::getPreference('admin/password/forgot_email_sender');
                        $template = Fox::getPreference('admin/password/forgot_email_template');
                        $modelTemplate->sendTemplateMail($template, $model->getEmail(), $sender, array('name' => $model->getFirstname(), 'link' => Fox::getUrl('*/*/change-password', array('user_id' => $model->getId(), 'code' => $code))));
                    } catch (Exception $e) {}
                    $this->sendRedirect('*/*/');
                } else {
                    throw new Exception('Invalid username was given.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Change passwod action
     * 
     * User is redirected to change password interface from the link sent via email for forget password
     */
    public function changePasswordAction() {
        if (Fox::getModel('admin/session')->getLoginData()) {
            $this->sendRedirect('*/dashboard/');
        }
        $userId = $this->getRequest()->getParam('user_id');
        $code = $this->getRequest()->getParam('code');
        $forgetPasswordModel = Fox::getModel('admin/forgetPassword');
        $forgetPasswordModel->load($userId, 'user_id');
        try {
            if ($forgetPasswordModel->getCode() == $code) {
                if ($this->getRequest()->isPost()) {
                    $data = $this->getRequest()->getPost();
                    if ($data['password'] && ($data['password'] == $data['cpassword'])) {
                        $model = Fox::getModel('admin/user');
                        $pwd = md5($data['password']);
                        $model->load($userId);
                        $model->setPassword($pwd);
                        $model->save();
                        $forgetPasswordModel->delete();
                        Fox::getHelper('core/message')->setInfo('Your password was successfully changed.');
                        $this->sendRedirect('*/*/');
                    } else {
                        throw new Exception('New password must match confirm password.');
                    }
                }
            } else {
                Fox::getHelper('core/message')->setError('Change password link was incorrect or expired.');
                $this->sendRedirect('*/*/');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Logout action
     */
    public function logoutAction() {
        Fox::getModel('admin/session')->unsetAllData();
        $this->sendRedirect('*/*/');
    }

}