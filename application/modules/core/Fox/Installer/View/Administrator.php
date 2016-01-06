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
 * Class Fox_Installer_View_Administrator
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Installer
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License 
 */
class Fox_Installer_View_Administrator extends Fox_Core_View_Template {

    /**
     * Form session data
     * 
     * @var array|NULL
     */
    private $_administratorData = NULL;

    /**
     * Public constructor
     */
    public function __construct() {
        parent::__construct();
        $this->_administratorData = Fox::getModel('installer/session')->getBackend();
    }

    /**
     * Get administrator setup url
     * 
     * @return string 
     */
    public function getAdministratorSetupUrl() {
        return Fox::getUrl('*/wizard/administrator');
    }

    /**
     * Retrieve first name from session data
     * 
     * @return string
     */
    public function getFirstName() {
        $defaultFirstName = '';
        if (isset($this->_administratorData['firstname']) && $this->_administratorData['firstname']) {
            $defaultFirstName = $this->_administratorData['firstname'];
        }
        return $defaultFirstName;
    }

    /**
     * Retrieve last name from session data
     * 
     * @return string
     */
    public function getLastName() {
        $defaultLastName = '';
        if (isset($this->_administratorData['lastname']) && $this->_administratorData['lastname']) {
            $defaultLastName = $this->_administratorData['lastname'];
        }
        return $defaultLastName;
    }

    /**
     * Retrieve email from session data
     * 
     * @return string
     */
    public function getEmail() {
        $defaultEmail = '';
        if (isset($this->_administratorData['email']) && $this->_administratorData['email']) {
            $defaultEmail = $this->_administratorData['email'];
        }
        return $defaultEmail;
    }

    /**
     * Retrieve contact from session data
     * 
     * @return string
     */
    public function getContact() {
        $defaultContact = '';
        if (isset($this->_administratorData['contact']) && $this->_administratorData['contact']) {
            $defaultContact = $this->_administratorData['contact'];
        }
        return $defaultContact;
    }

    /**
     * Retrieve username from session data
     * 
     * @return string
     */
    public function getUsername() {
        $defaultUsername = '';
        if (isset($this->_administratorData['username']) && $this->_administratorData['username']) {
            $defaultUsername = $this->_administratorData['username'];
        }
        return $defaultUsername;
    }

}