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
 * Class Fox_Member_Controller_Action
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_Controller_Action extends Fox_Core_Controller_Action {

    /**
     * Controller predispatch method
     *
     * Called before action method
     */
    function preDispatch() {
        parent::preDispatch();
        if (!Fox::getModel('member/session')->getLoginData()) {
            if (!('member' === strtolower($this->getRequest()->getModuleName()) && 'account' === strtolower($this->getRequest()->getControllerName()) && 'login' === strtolower($this->getRequest()->getActionName())) && !('member' === strtolower($this->getRequest()->getModuleName()) && 'account' === strtolower($this->getRequest()->getControllerName()) && 'create' === strtolower($this->getRequest()->getActionName())) && !('member' === strtolower($this->getRequest()->getModuleName()) && 'account' === strtolower($this->getRequest()->getControllerName()) && 'forgot-password' === strtolower($this->getRequest()->getActionName())) && !('member' === strtolower($this->getRequest()->getModuleName()) && 'account' === strtolower($this->getRequest()->getControllerName()) && 'verification' === strtolower($this->getRequest()->getActionName())) && !('member' === strtolower($this->getRequest()->getModuleName()) && 'account' === strtolower($this->getRequest()->getControllerName()) && 'change-forget-password' === strtolower($this->getRequest()->getActionName()))) {
                $this->sendRedirect('member/account/login');
            }
        }else{
            Fox::getModel('member/session')->setSessionTimeout(Fox::getMemberSessionInterval());
        }
    }

}