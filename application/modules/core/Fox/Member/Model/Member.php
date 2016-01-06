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
 * Class Fox_Member_Model_Member
 * 
 * @uses Fox_Eav_Model_Abstract
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_Model_Member extends Fox_Eav_Model_Abstract {
    const STATUS_BLOCKED=0;
    const STATUS_ACTIVE=1;
    const STATUS_NOT_CONFIRM=2;
    const MEMBER_REGISTRATION_DISABLED =0;
    const MEMBER_REGISTRATION_ENABLED =1;
    const LOGIN_REDIRECT_ALLOWED_NO = 0;
    const LOGIN_REDIRECT_ALLOWED_YES = 1;
    const ENTITY_TYPE_MEMBER = 'member';
    /**
     * Member setting data
     * 
     * @var array
     */
    protected static $_config;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'member_entity';
        /**
         * Entity type code for member
         */
        $this->_eav_entity_type = self::ENTITY_TYPE_MEMBER;
        parent::__construct();
    }

    /**
     * Get member setting data
     * 
     * @return array
     */
    public static function _getConfig() {
        if (empty(self::$_config)) {
            $configData = Fox::getModel('core/preference')->getPreferenceData('member_configuration');
            if (isset($configData['member_configuration'])) {
                self::$_config = $configData['member_configuration'];
            }
        }
        if (!isset(self::$_config['account'])) {
            throw new Exception('Account settings was not found.');
        }
        return self::$_config;
    }

    /**
     * Get all status options
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', Fox_Member_Model_Status::STATUS_BLOCKED => 'Disabled', Fox_Member_Model_Status::STATUS_ACTIVE => 'Enabled', Fox_Member_Model_Status::STATUS_NOT_CONFIRM => 'Not Confirmed');
    }

    /**
     * Get all status for group action
     * 
     * @return array
     */
    public function getGroupStatuses() {
        return array('' => '', Fox_Member_Model_Status::STATUS_BLOCKED => 'Disabled', Fox_Member_Model_Status::STATUS_ACTIVE => 'Enabled');
    }

    /**
     * Create new member
     * 
     * @param array $data
     * @return Fox_Member_Model_Member
     */
    public function createMember($data) {
        $code = NULL;
        $password = $data['password'];
        $confirmPassword = $data['confirm_password'];
        if ($password != $confirmPassword) {
            throw new Exception('Password must match confirm password.');
        }
        $setModel = Fox::getModel('eav/set');
        $setDetail = $setModel->getAttributeSetByEntity($this->getEavEntityType());
        if (!$setDetail) {
            throw new Exception('Attribute set was not found.');
        }
        $this->unsetData();
        $this->getAdapter()->beginTransaction();
        try {
            $this->setData($data);
            $this->setAttributeSetId($setDetail['id']);
            $this->setPassword(md5($password));
            $this->setFirstName(ucwords($data['first_name']));
            $this->setLastName(ucwords($data['last_name']));
            $curDate = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
            $this->setJoinDate($curDate);
            $isConfirmationRequired = Fox::getPreference('member/account/required_email_confirmation');
            if ($isConfirmationRequired) {
                $this->setStatus(Fox_Member_Model_Status::STATUS_NOT_CONFIRM);
            } else {
                $this->setStatus(Fox_Member_Model_Status::STATUS_ACTIVE);
            }
            $this->save();
            $this->getAdapter()->commit();
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            throw new Exception($e->getMessage());
        }
        $vars['name'] = $this->getFirstName();
        $vars['email'] = $this->getEmailId();
        $vars['password'] = $password;
        $this->sendWelcomeMail($vars);
        if ($isConfirmationRequired) {
            $code = ( strtolower(substr(md5(time() * mt_rand()), 0, 20)));
            $verificationModel = Fox::getModel('member/verification');
            $verificationModel->load($this->getId(), 'mem_id');
            $verificationModel->setMemId($this->getId());
            $verificationModel->setVerificationCode($code);
            $verificationModel->save();
            $vars['link'] = Fox::getUrl('member/account/verification', array('id' => $this->getId(), 'code' => $code));
            $this->sendConfirmationLinkMail($vars);
        }
        return $this;
    }

    /**
     * Resize member image
     * 
     * @param string|NULL $img Image name
     * @param int $width Target width
     * @param int $height Target height
     * @return string Returns the url of the resized image
     * @throws Exception if member upload directory not found
     */
    public function getResizedMemberImage($img=NULL, $width=100, $height=100) {
        $image = '';
        $resizePath = $width . 'x' . $height;
        if ($img != NULL) {
            $image = $img;
        } else if ($this->getMemberImage()) {
            $image = $this->getMemberImage();
        }
        $destinationPath = self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath;
        $destinationImagePath = $destinationPath . DIRECTORY_SEPARATOR . $image;
        $destinationBasePath = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR;
        if (!$image || !file_exists($destinationBasePath . $destinationImagePath)) {
            if (!file_exists($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath)) {
                if (!@mkdir($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath, 0777, TRUE)) {
                    throw new Exception('"' . $destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath . '" path was not found.');
                }
                @chmod($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath, 0777);
            }
            if ($image && file_exists($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $image)) {
                $thumb = new Uni_Util_ImageEditor();
                $thumb->resize($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $image, $width, $height, $image, $destinationBasePath . $destinationPath . DIRECTORY_SEPARATOR, false);
                @chmod($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath, 0777);
            } else {
                $image = 'no-image.png';
                $destinationImagePath = $destinationPath . DIRECTORY_SEPARATOR . $image;
                if (file_exists($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $image)) {
                    $thumb = new Uni_Util_ImageEditor();
                    $thumb->resize($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $image, $width, $height, $image, $destinationBasePath . $destinationPath . DIRECTORY_SEPARATOR, false);
                    @chmod($destinationBasePath . self::ENTITY_TYPE_MEMBER . DIRECTORY_SEPARATOR . $resizePath, 0777);
                }
            }
        }
        $destinationImagePath = str_replace(DIRECTORY_SEPARATOR, "/", $destinationImagePath);
        return Fox::getUploadDirectoryUrl() . '/' . $destinationImagePath;
    }

    /**
     * Retrieve admin actions for member
     * 
     * @return array
     */
    public function getMemberAction() {
        return array('change_password' => Fox::getUrl('*/admin_password/change-password', array('id' => $this->getId())), 'login' => Fox::getUrl('*/*/member-login', array('id' => $this->getId())));
    }

    /**
     * Retrieve active members as array when source model is used for member
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray(array $fields) {
        $options = array('' => 'Select');
        $collection = $this->getCollection('status=' . Fox_Member_Model_Status::STATUS_ACTIVE, '*', 'first_name ASC');
        foreach ($collection as $user) {
            $options[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
        }
        return $options;
    }

    /**
     * Send new account welcome email
     * 
     * @param array $vars Email template parameters
     */
    public function sendWelcomeMail($vars=array()) {
        try {
            $sender = Fox::getPreference('member/account/email_sender');
            $template = Fox::getPreference('member/account/default_welcome_email');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $this->getEmailId(), $sender, $vars);
        } catch (Exception $e) {}
    }

    /**
     * Send new account confirmation email having confirmation link
     * 
     * @param array $vars Email template parameters
     */
    public function sendConfirmationLinkMail($vars=array()) {
        try {
            $sender = Fox::getPreference('member/account/email_sender');
            $template = Fox::getPreference('member/account/confirmation_link_email');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $this->getEmailId(), $sender, $vars);
        } catch (Exception $e) {}
    }

    /**
     * Send account confirmed email
     * 
     * @param array $vars Email template parameters
     */
    public function sendConfirmedMail($vars=array()) {
        try {
            $sender = Fox::getPreference('member/account/email_sender');
            $template = Fox::getPreference('member/account/confirmed_email');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $this->getEmailId(), $sender, $vars);
        } catch (Exception $e) {}
    }
    
    /**
     * Send password changed email
     * 
     * @param array $vars Email template parameters
     */
    public function sendPasswordChangedMail($password) {
        try {
            $sender = Fox::getPreference('member/password/email_sender');
            $template = Fox::getPreference('member/password/change_password_email');
            $modelTemplate = Fox::getModel('core/email/template');
            $vars=array('name'=>$this->getFirstName(),'email'=>$this->getEmailId(),'password'=>$password);
            $modelTemplate->sendTemplateMail($template, $this->getEmailId(), $sender, $vars);
        } catch (Exception $e) {}
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array
     * @throws Exception if member email already exists
     */
    protected function _preSave(array $data) {
        $data = parent::_preSave($data);
        if ($this->isEmailExist($data['email_id'], isset($data['id']) && $data['id'] ? $data['id'] : 0)) {
            throw new Exception('Email Id already exists.');
        }
        return $data;
    }

    /**
     * Checks whether the email already exists
     * 
     * @param string $emailId member email id
     * @param int $id member id
     * @return boolean TRUE if email already exists
     */
    private function isEmailExist($emailId, $id=0) {
        return $this->getAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name WHERE email_id=? AND id!=?", array($emailId, $id));
    }

    /**
     * Retrieve upload path for member images
     * 
     * @return string
     */
    public static function getImageUploadPath() {
        return Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . self::ENTITY_TYPE_MEMBER;
    }

}