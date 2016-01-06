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
 * Class Fox_Newsletter_Model_Template
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Newsletter_Model_Template extends Uni_Core_Model {
    const STATUS_NOT_SENT=0;
    const STATUS_SENT=1;
    /**
     * Email templates
     * 
     * @var array
     */
    protected $templateInfo;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'newsletter_template';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get status options
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_SENT => 'Sent', self::STATUS_NOT_SENT => 'Not Sent');
    }

    /**
     * Get admin actions
     * 
     * @return array
     */
    public function getAction() {
        return array('send_newsletter' => Fox::getUrl('*/*/send', array('id' => $this->getId())), 'preview_url' => Fox::getUrl('*/admin_template/preview', array('tId' => $this->getId())));
    }

    /**
     * Send newsletter to subscribers
     */
    public function sendNewsletter() {
        $subscriberModel = Fox::getModel('newsletter/subscriber');
        $subscriberCollection = $subscriberModel->getCollection('status=' . Fox_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
        $currDate = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
        $modelTemplate = Fox::getModel('core/email/template');
        foreach ($subscriberCollection as $subscriber) {
            $subject = $modelTemplate->getParsedContent($this->getSubject(),array('email'=>$subscriber['email'],'name'=>$subscriber['name']));
            $message = $modelTemplate->getParsedContent($this->getContent(),array('email'=>$subscriber['email'],'name'=>$subscriber['name']));
            $modelTemplate->sendMail($subscriber['email'], $this->getSenderEmail(), $this->getSenderName(), $subject, $message);
        }
        $this->setStatus(Fox_Newsletter_Model_Template::STATUS_SENT);
        $this->setLastSent($currDate);
        $this->save();
    }

    /**
     * Get email templates
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray(array $fields) {
        if (empty($this->templateInfo)) {
            $data = $this->getCollection();
            $this->templateInfo = array();
            foreach ($data as $row) {
                $this->templateInfo[$row['id']] = $row['name'];
            }
        }
        return $this->templateInfo;
    }

}