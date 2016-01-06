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
 * Class Fox_Core_Model_Email_Template
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Email_Template extends Uni_Core_Model {

    /**
     * Email templates
     * 
     * @var array
     */
    protected $emailTempInfo;
    /**
     * Used for internal processing
     * 
     * @var array 
     */
    protected static $_tplVars = array();

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'core_email_template';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get template model object
     * 
     * @param int $id
     * @return Fox_Core_Model_Email_Template 
     */
    public function getTemplateData($id) {
        return $this->load($id);
    }

    /**
     * Get all templates
     * 
     * @return array
     */
    public function getAllTemplates() {
        $data = $this->getCollection();
        $template = array();
        $template[''] = 'Select Template name';
        foreach ($data as $row) {
            $template[$row['id']] = $row['name'];
        }
        return $template;
    }

    /**
     * Get templates for source model call
     * 
     * @param array $fields
     * @return array 
     */
    public function _getOptionArray(array $fields) {
        if (empty($this->emailTempInfo)) {
            $data = $this->getCollection();
            $this->emailTempInfo = array();
            foreach ($data as $row) {
                $this->emailTempInfo[$row['id']] = $row['name'];
            }
        }
        return $this->emailTempInfo;
    }

    /**
     * Get parsed template content
     * 
     * @param string $data
     * @param array $vars
     * @return string
     */
    public function getParsedContent($data, $vars=array()) {
        $data = addslashes($data);
        self::$_tplVars = $vars;
        $parsedData = '';
        $pat = '/{{([^{}]+)}}/';
        if (!empty($vars)) {
            $replaceWith = '\${vars[\'${1}\']}';
        } else {
            $replaceWith = '';
        }
        $content = preg_replace_callback($pat, 'Fox_Core_Model_Email_Template::replaceMatched', $data);
        eval('$parsedData="' . $content . '";');
        self::$_tplVars = array();
        return html_entity_decode(stripslashes($parsedData));
    }

    /**
     * Replace template variable with matched data
     * 
     * @param array $matched
     * @return string 
     */
    protected static function replaceMatched($matched) {
        $replacement = '';
        if (is_array($matched) && count($matched) > 1) {
            $variables = explode(' ', $matched[1]);
            if (count($variables) < 1 || !$variables[0]) {
                return '';
            }
            $keyword = $variables[0];
            if (is_array(self::$_tplVars) && array_key_exists($keyword, self::$_tplVars)) {
                $replacement = self::$_tplVars[$keyword];
            } else {
                $params = '';
                $methodArr = explode('=', $keyword);
                $methodName = $methodArr[0];
                if (count($methodArr) > 1) {
                    $params = $methodArr[1];
                }
                $funcName = 'get' . ucfirst($methodName);
                if (method_exists('Fox_Core_Model_Email_Config', $funcName)) {
                    if ($params) {
                        $replacement = Fox_Core_Model_Email_Config::$funcName($params);
                    } else {
                        $replacement = Fox_Core_Model_Email_Config::$funcName();
                    }
                }
            }
        }
        return addslashes($replacement);
    }

    /**
     * Send email
     * 
     * @param string $to Recipient email
     * @param string $senderEmail Sender email
     * @param string $senderName Sender name
     * @param string $subject Email subject
     * @param string $body Email body
     * @param array $bcc Bcc email ids 
     */
    public function sendMail($to, $senderEmail, $senderName, $subject, $body, $bcc=array()) {
        $mail = new Zend_Mail();
        $mail->addHeader('MIME-Version', '1.0');
        $mail->addHeader('Content-Transfer-Encoding', '8bit');
        $mail->addHeader('X-Mailer:', 'PHP/' . phpversion());
        $mail->addHeader("From ", $senderEmail);
        $mail->setSubject($subject);
        $mail->setBodyHtml($body);
        $mail->setFrom($senderEmail, $senderName);
        $mail->addTo($to);
        if (!empty($bcc)) {
            foreach ($bcc as $email) {
                $mail->addBcc($email);
            }
        }
        $mail->send();
    }

    /**
     * Send template email
     * 
     * @param int $templateId Template id
     * @param string $to Recipient email
     * @param mixed $sender array|string
     * @param array $vars Template variables
     * @param array $bcc Bcc email ids
     * @throws Exception if template not found
     */
    public function sendTemplateMail($templateId, $to, $sender, $vars=array(), $bcc=array()) {
        $this->load($templateId);
        if (!$this->getId()) {
            throw new Exception('Email template was not found.');
        }
        if (!is_array($sender)) {
            $senderName = Fox::getPreference('website_email_addresses/' . $sender . '/sender_name');
            $senderEmail = Fox::getPreference('website_email_addresses/' . $sender . '/sender_email');
        } else {
            $senderName = isset($sender['sender_name']) ? $sender['sender_name'] : '';
            $senderEmail = isset($sender['sender_email']) ? $sender['sender_email'] : '';
        }
        $subject = $this->getParsedContent($this->getSubject(), $vars);
        $body = $this->getParsedContent($this->getContent(), $vars);
        $mail = new Zend_Mail();
        $mail->addHeader('MIME-Version', '1.0');
        $mail->addHeader('Content-Transfer-Encoding', '8bit');
        $mail->addHeader('X-Mailer:', 'PHP/' . phpversion());
        $mail->addHeader("From ", $senderEmail);
        $mail->setSubject($subject);
        $mail->setBodyHtml($body);
        $mail->setFrom($senderEmail, $senderName);
        $mail->addTo($to);
        if (!empty($bcc)) {
            foreach ($bcc as $email) {
                $mail->addBcc($email);
            }
        }
        $mail->send();
    }

}