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
 * Class Fox_Contact_Model_Contact
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Contact
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Contact_Model_Contact extends Uni_Core_Model {
    const STATUS_UNREAD=0;
    const STATUS_READ=1;
    const STATUS_REPLIED=2;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'contact_request';
        /**
         * Table primary key field 
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get contact reply url
     * 
     * @return string
     */
    public function getContactAction() {
        return Fox::getUrl('*/admin_contact/reply', array('id' => $this->getId()));
    }

    /**
     * Get all status
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_UNREAD => 'Unread', self::STATUS_READ => 'Read', self::STATUS_REPLIED => 'Replied');
    }

    /**
     * Send reply to contact request
     * 
     * @param string $replyMessage 
     */
    public function reply($replyMessage) {
        $this->setStatus(Fox_Contact_Model_Contact::STATUS_REPLIED);
        $this->save();
        $modelTemplate = Fox::getModel('core/email/template');
        $modelTemplate->sendTemplateMail(Fox::getPreference('contact/reply/email_template'), $this->getEmail(), array('sender_email' => Fox::getPreference('contact/reply/email'), 'sender_name' => Fox::getPreference('contact/reply/name')), array('name' => $this->getName(), 'subject' => $this->getSubject(), 'message' => $this->getMessage(), 'reply' => $replyMessage));
    }

}