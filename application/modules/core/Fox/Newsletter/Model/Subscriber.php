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
 * Class Fox_Newsletter_Model_Subscriber
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Newsletter_Model_Subscriber extends Uni_Core_Model {
    const STATUS_UNSUBSCRIBED=0;
    const STATUS_CONFIRMATION_REQUIRED=1;
    const STATUS_SUBSCRIBED=2;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'newsletter_subscriber';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get status options
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_UNSUBSCRIBED => 'Unsubscribed', self::STATUS_SUBSCRIBED => 'Subscribed', self::STATUS_CONFIRMATION_REQUIRED => 'Unconfirmed');
    }

    /**
     * Subscribe for newsletters
     * 
     * Sends confirmation link email if confirmation is rerquired otherwise success email is sent
     * 
     * @param array $data 
     */
    public function subscribe($data) {
        $code = ( strtolower(substr(md5(time() * mt_rand()), 0, 20)));
        $this->load($data['email'], 'email');
        $isConfirmationRequired = Fox::getPreference('newsletter/subscription/need_to_confirm');
        if ($isConfirmationRequired) {
            $this->setStatus(self::STATUS_CONFIRMATION_REQUIRED);
        } else {
            $this->setStatus(self::STATUS_SUBSCRIBED);
        }
        $data['code'] = $code;
        $this->setData($data);
        $this->save();
        $vars['name'] = $this->getName();
        if ($isConfirmationRequired) {
            $vars['link'] = Fox::getUrl('newsletter/subscriber/confirm', array('id' => $this->getId(), 'code' => $code));
            $this->sendConfirmationLinkMail($this->getEmail(), $vars);
        } else {
            $vars['unsubscribe_link'] = Fox::getUrl('newsletter/subscriber/unsubscribe', array('id' => $this->getId(), 'code' => $code));
            $this->sendSubscriptionSuccessMail($this->getEmail(), $vars);
        }
    }

    /**
     * Confirm newsletter subscription
     * 
     * Sends subscription success email
     * 
     * @param string $code
     * @return boolean
     */
    public function confirm($code) {
        if ($this->getCode() == $code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                    ->save();
            $vars['name'] = $this->getName();
            $vars['unsubscribe_link'] = Fox::getUrl('newsletter/subscriber/unsubscribe', array('id' => $this->getId(), 'code' => $code));
            $this->sendSubscriptionSuccessMail($this->getEmail(), $vars);
            return true;
        }
        return false;
    }

    /**
     * Unsubscribe for newsletters
     * 
     * Sends unsubscription success email
     * 
     * @return Fox_Newsletter_Model_Subscriber|FALSE
     */
    public function unsubscribe() {
        if ($this->getStatus() != self::STATUS_UNSUBSCRIBED) {
            $this->setStatus(self::STATUS_UNSUBSCRIBED)
                    ->save();
            $this->sendUnsubscriptionMail($this->getEmail());
            return $this;
        }
        return FALSE;
    }

    /**
     * Send newsletter subscription success email
     * 
     * @param string $email Email id
     * @param array $vars Email template parameters
     */
    public function sendSubscriptionSuccessMail($email, $vars) {
        try {
            $sender = Fox::getPreference('newsletter/subscription/success_email_sender');
            $template = Fox::getPreference('newsletter/subscription/success_email_template');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $email, $sender, $vars);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Send newsletter confirmation link email
     * 
     * @param string $email Email id
     * @param array $vars Email template parameters
     */
    public function sendConfirmationLinkMail($email, $vars) {
        try {
            $sender = Fox::getPreference('newsletter/subscription/confirmation_email_sender');
            $template = Fox::getPreference('newsletter/subscription/confirmation_email_template');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $email, $sender, $vars);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Send newsletter unsubscription success email
     * 
     * @param string $email Email id
     * @param array $vars Email template parameters
     */
    public function sendUnsubscriptionMail($email, $vars=array()) {
        try {
            $sender = Fox::getPreference('newsletter/subscription/unsubscription_email_sender');
            $template = Fox::getPreference('newsletter/subscription/unsubscription_email_template');
            $modelTemplate = Fox::getModel('core/email/template');
            $modelTemplate->sendTemplateMail($template, $email, $sender, $vars);
        } catch (Exception $e) {
            
        }
    }

}