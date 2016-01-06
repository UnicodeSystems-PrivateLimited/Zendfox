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
 * Class Fox_Admin_Helper_Message
 * 
 * @uses Uni_Core_Session
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Helper_Message extends Uni_Core_Session {
    const TYPE_INFO=0;
    const TYPE_ERROR=1;
    const TYPE_WARNING=2;
    /**
     * Success messages
     * 
     * @var array 
     */
    protected $infos = array();
    /**
     * Error messages
     * 
     * @var array 
     */
    protected $errors = array();
    /**
     * Warning messages
     * 
     * @var array 
     */
    protected $warnings = array();
    /**
     * Css class names
     * 
     * @var array
     */
    protected $elClasses = array('info', 'error', 'warning');

    /**
     * Set the success message
     * 
     * @param string $message 
     */
    function setInfo($message) {
        $this->infos = array();
        $this->addInfo($message);
    }

    /**
     * Set the error message
     * 
     * @param string $message 
     */
    function setError($message) {
        $this->errors = array();
        $this->addError($message);
    }

    /**
     * Set the warning message
     * 
     * @param string $message 
     */
    function setWarning($mesaage) {
        $this->warnings = array();
        $this->addWarning($mesaage);
    }

    /**
     * Adds the success message to the queue
     * 
     * @param string $message 
     */
    function addInfo($message) {
        if (($infos = $this->getInfoMessages())) {
            $infos[] = $message;
        } else {
            $this->setInfoMessages(array($message));
        }
    }

    /**
     * Adds the error message to the queue
     * 
     * @param string $message 
     */
    function addError($message) {
        if (($errors = $this->getErrorMessages())) {
            $errors[] = $message;
        } else {
            $this->setErrorMessages(array($message));
        }
    }

    /**
     * Adds the warning message to the queue
     * 
     * @param string $message 
     */
    function addWarning($mesaage) {
        if (($warnings = $this->getWarningMessages())) {
            $warnings[] = $message;
        } else {
            $this->setWarningMessages(array($message));
        }
    }

    /**
     * Retrieve success messages
     * 
     * @param boolean $autoClean Empty current success message queue
     * @return array
     */
    function getInfos($autoClean=TRUE) {
        $retVal = $this->getInfoMessages();
        $autoClean && $this->unsetInfoMessages();
        return $retVal;
    }

    /**
     * Retrieve error messages
     * 
     * @param boolean $autoClean Empty current error message queue
     * @return array
     */
    function getErrors($autoClean=TRUE) {
        $retVal = $this->getErrorMessages();
        $autoClean && $this->unsetErrorMessages();
        return $retVal;
    }

    /**
     * Retrieve warning messages
     * 
     * @param boolean $autoClean Empty current warning message queue
     * @return array
     */
    function getWarnings($autoClean=TRUE) {
        $retVal = $this->getWarningMessages();
        $autoClean && $this->unsetWarningMessages();
        return $retVal;
    }

    /**
     * Retrieve all messages
     * 
     * @param boolean $autoClean Empty current message queues
     * @return array Returns Success|Error|Warning Messages
     */
    function getAllMessages($autoClean=FALSE) {
        $messages = array(self::TYPE_INFO => $this->getInfoMessages(), self::TYPE_ERROR => $this->getErrorMessages(), self::TYPE_WARNING => $this->getWarningMessages());
        if ($autoClean) {
            $this->unsetInfoMessages();
            $this->unsetErrorMessages();
            $this->unsetWarningMessages();
        }
        return $messages;
    }

    /**
     * Get success messages as html
     * 
     * @return string 
     */
    function getInfoHtml() {
        $html = FALSE;
        if (!empty($this->getInfoMessages())) {
            $html = '<ul class="messages">';
            foreach ($this->getInfoMessages() as $info) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_INFO] . '">' . $info . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

    /**
     * Get error messages as html
     * 
     * @return string 
     */
    function getErrorHtml() {
        $html = FALSE;
        if (!empty($this->getErrorMessages())) {
            $html = '<ul class="messages">';
            foreach ($this->getErrorMessages() as $error) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_ERROR] . '">' . $error . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

    /**
     * Get warning messages as html
     * 
     * @return string 
     */
    function getWarningHtml() {
        $html = FALSE;
        if (!empty($this->getInfoMessages())) {
            $html = '<ul class="messages">';
            foreach ($this->getWarningMessages() as $warning) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_WARNING] . '">' . $warning . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

    /**
     * Get all messages as html
     * 
     * @param $autoClean Empty current message queues
     * @return string Returns Success|Error|Warning Messages
     */
    function getAllMessagesHtml($autoClean=TRUE) {
        $html = FALSE;
        if (!(empty($this->getInfoMessages()) && empty($this->getErrorMessages()) && empty($this->getWarningMessages()))) {
            $html = '<ul class="messages">';
            foreach ($this->getInfoMessages() as $info) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_INFO] . '">' . $info . '</li>';
            }
            foreach ($this->getErrorMessages() as $error) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_ERROR] . '">' . $error . '</li>';
            }
            foreach ($this->getWarningMessages() as $warning) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_WARNING] . '">' . $warning . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

}