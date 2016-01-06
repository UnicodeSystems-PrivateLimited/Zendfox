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
 * Class Fox_Member_Helper_Data
 * 
 * @uses Uni_Core_Helper
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_Helper_Data extends Uni_Core_Helper {

    /**
     * Get member login url
     * 
     * @return string
     */
    public function getLoginUrl() {
        return Fox::getUrl('member/account/login');
    }

    /**
     * Get member home page url
     * 
     * @return string
     */
    public function getMemberHomeUrl() {
        return Fox::getUrl('member');
    }

    /**
     * Get member profile view url
     * 
     * @return string
     */
    public function getMemberProfileUrl() {
        return Fox::getUrl('member/account/profile');
    }

    /**
     * Get member logout url
     * 
     * @return string
     */
    public function getLogoutUrl() {
        return Fox::getUrl('member/account/logout');
    }

    /**
     * Get member registration url
     * 
     * @return string
     */
    public function getRegisterUrl() {
        return Fox::getUrl('member/account/create');
    }

    /**
     * Get member forget password form url
     * 
     * @return string
     */
    public function getForgetPasswordUrl() {
        return Fox::getUrl('member/account/forgot-password');
    }

    /**
     * Get member change password url
     * 
     * @return string
     */
    public function getChangePasswordUrl() {
        return Fox::getUrl('member/account/change-password');
    }

    /**
     * Get member account verification url
     * 
     * @return string
     */
    public function getVerificationUrl() {
        return Fox::getUrl('member/account/verification');
    }

    /**
     * Get edit image form url
     * 
     * @return string
     */
    public function getEditImageUrl() {
        return Fox::getUrl('member/account/edit-image');
    }

    /**
     * Get member remove image url
     * 
     * @return string
     */
    public function getRemoveImageUrl() {
        return Fox::getUrl('member/account/remove-image');
    }

}