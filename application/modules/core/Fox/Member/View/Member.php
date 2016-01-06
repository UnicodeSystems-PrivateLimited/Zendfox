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
 * Class Fox_Member_View_Member
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Member extends Fox_Core_View_Template {

    /**
     * Current logined member
     * 
     * @var Fox_Member_Model_Member|NULL
     */
    protected $currentMember;

    /**
     * Get current logined member
     * 
     * @return Fox_Member_Model_Member|NULL
     */
    public function getCurrentMember() {
        if (is_null($this->currentMember)) {
            $session = Fox::getModel('member/session');
            $this->currentMember = $session->getLoginData();
        }
        return $this->currentMember;
    }

    /**
     * Get member login url 
     * 
     * @return string
     */
    function getLoginUrl() {
        return Fox::getHelper('member/data')->getLoginUrl();
    }

    /**
     * Get member logout url
     * 
     * @return string
     */
    function getLogoutUrl() {
        return Fox::getHelper('member/data')->getLogoutUrl();
    }

    /**
     * Get member registration url
     * 
     * @return string
     */
    function getRegisterUrl() {
        return Fox::getHelper('member/data')->getRegisterUrl();
    }

}