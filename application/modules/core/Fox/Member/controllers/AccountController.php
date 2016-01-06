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
 * Class Member_AccountController
 * 
 * @uses Fox_Member_Controller_Action
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Member_AccountController extends Fox_Member_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->sendForward('*/*/login');
    }

    /**
     * Create account action
     */
    public function createAction() {
        if(Fox::getPreference('member/account/registration') != Fox_Member_Model_Member::MEMBER_REGISTRATION_ENABLED){
            $this->sendRedirect('*/*/login');
            return;
        }
        $model = Fox::getModel('member/member');
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            try {
                $member = $model->createMember($data);
                if (Fox::getPreference('member/account/required_email_confirmation')) {
                    Fox::getHelper('core/message')->setInfo('Account confirmation is required. Please check your email for the confirmation link.');
                    $this->sendRedirect('*/*/login');
                    return;
                } else {
                    Fox::getModel('member/session')->setLoginData($member);
                    $this->sendRedirect('member');
                    return;
                }
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Login action
     * 
     * @return void
     */
    public function loginAction() {
        if (Fox::getLoggedMember()) {
            $this->sendRedirect('member');
            return;
        }
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($data['email_id'] && $data['password']) {
                    $memberModel = Fox::getModel('member/member');
                    $memberModel->load(array('email_id' => $data['email_id'], 'password' => md5($data['password'])));
                    if ($memberModel->getId()) {
                        if ($memberModel->getStatus() == Fox_Member_Model_Status::STATUS_NOT_CONFIRM) {
                            Fox::getHelper('core/message')->addError('Please verify your account.');
                        } else if ($memberModel->getStatus() == Fox_Member_Model_Status::STATUS_BLOCKED) {
                            Fox::getHelper('core/message')->addError('Your account is blocked.');
                        } else {
                            Fox::getModel('member/session')->setLoginData($memberModel);
                            if (Fox::getPreference('member/login/login_redirect_allowed') == Fox_Member_Model_Member::LOGIN_REDIRECT_ALLOWED_YES) {
                                $this->sendRedirect('member');
                            } else {
                                $this->sendRedirect('');
                            }
                        }
                    } else {
                        Fox::getHelper('core/message')->addError('Invalid email id or password.');
                    }
                } else {
                    Fox::getHelper('core/message')->addError('Invalid email id or password.');
                }
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
        Fox::getModel('member/session')->unsetAllData();
        $this->sendRedirect('');
    }

    /**
     * Change password action
     */
    public function changePasswordAction() {
        try {
            if ($this->getRequest()->isPost()) {
                $memberData = Fox::getLoggedMember();
                $data = $this->getRequest()->getPost();
                $memberModel = Fox::getModel('member/member');
                if ($memberData->getPassword() == md5($data['current_password'])) {
                    if ($data['password'] == $data['c_password']) {
                        $memberModel->load($memberData->getId());
                        $memberModel->setPassword(md5($data['password']));
                        $memberModel->save();
                        $memberModel->sendPasswordChangedMail($data['password']);
                        $memberData->setPassword(md5($data['password']));
                        Fox::getModel('member/session')->setLoginData($memberData);
                        Fox::getHelper('core/message')->setInfo('Password was successfully changed.');
                    } else {
                        Fox::getHelper('core/message')->addError('New password must match confirm new password.');
                    }
                } else {
                    Fox::getHelper('core/message')->addError('Invalid current password.');
                }
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * View profile action
     */
    public function profileAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit profile action
     */
    public function editProfileAction() {
        if ($this->getRequest()->isPost()) {
            $member = Fox::getModel('member/member');
            $member->getAdapter()->beginTransaction();
            try {
                $id = Fox::getLoggedMember()->getId();
                $member->load($id);
                $data = $this->getRequest()->getPost();
                $member->setData($data);
                $member->save();
                $member->getAdapter()->commit();
                Fox::getHelper('core/message')->setInfo('Profile was successfully updated.');
                Fox::getModel('member/session')->setLoginData($member);
            } catch (Exception $e) {
                $member->getAdapter()->rollback();
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit image action
     */
    public function editImageAction() {
        if ($this->getRequest()->isPost()) {
            $memberModel = Fox::getModel('member/member');
            $memberModel->getAdapter()->beginTransaction();
            try {
                $data = $this->getRequest()->getPost();
                $memberModel->load(Fox::getLoggedMember()->getId());
                if (!empty($_FILES)) {
                    $path = Fox_Member_Model_Member::getImageUploadPath();
                    if (($pos = strrpos($_FILES['member_image']['name'], '.')) > -1) {
                        $ext = substr($_FILES['member_image']['name'], ($pos + 1));
                        $prevImage = $memberModel->getMemberImage();
                        $memberModel->save();
                        $memberModel->getAdapter()->commit();
                        if ($prevImage && (file_exists($path . DIRECTORY_SEPARATOR . $prevImage))) {
                            @unlink($path . DIRECTORY_SEPARATOR . $prevImage);
                        }
                        Fox::getModel('member/session')->setLoginData($memberModel);
                        Fox::getHelper('core/message')->setInfo('Profile image was successfully saved.');
                    }
                }
            } catch (Exception $e) {
                $memberModel->getAdapter()->rollback();
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Remove image action
     */
    public function removeImageAction() {
        $memberModel = Fox::getModel('member/member');
        $memberModel->getAdapter()->beginTransaction();
        try {
            $memberData = Fox::getLoggedMember();
            $memberModel->load($memberData->getId());
            $path = Fox_Member_Model_Member::getImageUploadPath();
            $prevImage = $memberModel->getMemberImage();
            $memberModel->setMemberImage('');
            $memberModel->save();
            $memberModel->getAdapter()->commit();
            if ($prevImage && (file_exists($path . DIRECTORY_SEPARATOR . $prevImage))) {
                @unlink($path . DIRECTORY_SEPARATOR . $prevImage);
            }
            Fox::getModel('member/session')->setLoginData($memberModel);
            Fox::getHelper('core/message')->setInfo('Profile image was successfully removed.');
        } catch (Exception $e) {
            $memberModel->getAdapter()->rollback();
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        $this->sendRedirect('*/*/edit-image');
    }

    /**
     * Forgot password action
     * 
     * Sends an email with link to member which redirects to change password interface
     */
    public function forgotPasswordAction() {
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $model = Fox::getModel('member/member');
                $model->load($data['email_id'], 'email_id');
                if ($model->getId()) {
                    $code = ( strtolower(substr(md5(time() * mt_rand()), 0, 20)));
                    $passwordModel = Fox::getModel('member/password');
                    $passwordModel->load($model->getId(), 'mem_id');
                    $passwordModel->setMemId($model->getId());
                    $passwordModel->setCode($code);
                    $passwordModel->setCreatedOn(Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s'));
                    $passwordModel->save();
                    try {
                        $modelTemplate = Fox::getModel('core/email/template');
                        $sender = Fox::getPreference('member/password/email_sender');
                        $template = Fox::getPreference('member/password/forgot_email');
                        $modelTemplate->sendTemplateMail($template, $model->getEmailId(), $sender, array('name' => $model->getFirstName(), 'link' => Fox::getUrl('*/password/change-password', array('code' => $code))));
                    } catch (Exception $e) {
                        
                    }
                    Fox::getHelper('core/message')->setInfo('Change password link has been sent to your email.');
                    $this->sendRedirect('*/*/login');
                } else {
                    Fox::getHelper('core/message')->setError('Email Id was not found.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Account confirmation action
     */
    public function verificationAction() {
        $id = $this->getRequest()->getParam('id', FALSE);
        $code = $this->getRequest()->getParam('code', FALSE);
        if ($id && $code) {
            $model = Fox::getModel('member/verification');
            $model->getAdapter()->beginTransaction();
            try {
                $model->load($id, 'mem_id');
                if ($model->getId()) {
                    if ($code == $model->getVerificationCode()) {
                        $memberModel = Fox::getModel('member/member');
                        $memberModel->load($id);
                        if ($memberModel->getId()) {
                            $memberModel->setStatus(Fox_Member_Model_Status::STATUS_ACTIVE);
                            $memberModel->save();
                            $model->delete();
                            $model->getAdapter()->commit();
                            $memberModel->sendConfirmedMail(array('name' => $memberModel->getFirstName()));
                            Fox::getHelper('core/message')->setInfo("Your account was successfully activated.");
                        }
                    } else {
                        Fox::getHelper('core/message')->setError('Invalid verification code.');
                    }
                } else {
                    Fox::getHelper('core/message')->setError('Record was not found.');
                }
            } catch (Exception $e) {
                $model->getAdapter()->rollback();
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->sendRedirect('*/*/login');
    }

}