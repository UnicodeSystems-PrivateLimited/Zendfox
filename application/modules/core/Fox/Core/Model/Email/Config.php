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
 * Class Fox_Core_Model_Email_Config
 * 
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Email_Config {
    const EMAIL_TYPE_GENERAL ='general';
    const EMAIL_TYPE_CUSTOM1 ='custom1';
    const EMAIL_TYPE_CUSTOM2 ='custom2';
    /**
     * Type of email contacts
     * 
     * @var array
     */
    protected $emailInfo;

    /**
     * Get email contacts
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray(array $fields) {
        if (empty($this->emailInfo)) {
            $this->emailInfo[] = '';
            $this->emailInfo[self::EMAIL_TYPE_GENERAL] = 'General Contact';
            $this->emailInfo[self::EMAIL_TYPE_CUSTOM1] = 'Custom1 Contact';
            $this->emailInfo[self::EMAIL_TYPE_CUSTOM2] = 'Custom2 Contact';
        }
        return $this->emailInfo;
    }

    /**
     * Get website name
     * 
     * @return string
     */
    public static function getWebsiteName() {
        return Fox::getPreference('general/website/name');
    }

    /**
     * Get website contact
     * 
     * @return string
     */
    public static function getWebsiteContact() {
        return Fox::getPreference('general/website/contact');
    }

    /**
     * Get website address
     * 
     * @return string
     */
    public static function getWebsiteAddress() {
        return Fox::getPreference('general/website/address');
    }

    /**
     * Get current theme url
     * 
     * @param string $url
     * @return string 
     */
    public static function getThemeUrl($url='') {
        return Fox::getThemeUrl($url, Uni_Controller_Action::MODE_WEB);
    }

    /**
     * Get url
     * 
     * @param string $url
     * @return string
     */
    public static function getUrl($url='') {
        return Fox::getUrl($url);
    }
    
    /**
     * Retrieve uploads url path
     * 
     * @return string
     */
    public static function getUploadDirectoryUrl() {
        return self::getSiteUrl() . UPLOAD_DIR;
    }

}