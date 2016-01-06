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
 * Class Member_PasswordController
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Member_PasswordController extends Fox_Core_Controller_Action {

    /**
     * Change password action
     * 
     * Member is redirected to change password interface from the link sent via email for forgot password
     */
    public function changePasswordAction() {
        $code = $this->getRequest()->getParam('code', FALSE);
        try {
            $model = Fox::getModel('member/password');
            if (!$code) {
                $this->sendRedirect('*/account/login');
            } else {
                $model->load($code, 'code');
                if (!$model->getId()) {
                    throw new Exception('Change password link was incorrect or expired.');
                }
            }
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($data['password'] == $data['c_password']) {
                    $memberModel = Fox::getModel('member/member');
                    $memberModel->load($model->getMemId());
                    if ($memberModel->getId()) {
                        $memberModel->setPassword(md5($data['password']));
                        $memberModel->save();
                        $memberModel->sendPasswordChangedMail($data['password']);
                        $model->delete();
                        Fox::getHelper('core/message')->setInfo('Password was successfully changed.');
                        $this->sendRedirect('*/account/login');
                    }
                } else {
                    Fox::getHelper('core/message')->setError('New password must match confirm new password.');
                }
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
            $this->sendRedirect('*/account/login');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

}