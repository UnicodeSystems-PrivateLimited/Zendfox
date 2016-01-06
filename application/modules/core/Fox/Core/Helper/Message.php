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
 * Class Fox_Core_Helper_Message
 * 
 * @uses Uni_Core_Session
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Helper_Message extends Uni_Core_Session {
    const TYPE_INFO=0;
    const TYPE_ERROR=1;
    const TYPE_WARNING=2;
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
        $this->addInfo($message, TRUE);
    }

    /**
     * Set the error message
     * 
     * @param string $message 
     */
    function setError($message) {
        $this->addError($message, TRUE);
    }

    /**
     * Set the warning message
     * 
     * @param string $message 
     */
    function setWarning($mesaage) {
        $this->addWarning($mesaage, TRUE);
    }

    /**
     * Adds the success message to the queue
     * 
     * @param string $message 
     * @param boolean $clean TRUE removes all the previous success messages
     */
    function addInfo($message, $clean=FALSE) {
        if ($clean || !($infos = $this->getInfoMessages())) {
            $infos = array();
        }
        $infos[] = $message;
        $this->setInfoMessages($infos);
    }

    /**
     * Adds the error message to the queue
     * 
     * @param string $message
     * @param boolean $clean TRUE removes all the previous error messages
     */
    function addError($message, $clean=FALSE) {
        if ($clean || !($errors = $this->getErrorMessages())) {
            $errors = array();
        }
        $errors[] = $message;
        $this->setErrorMessages($errors);
    }

    /**
     * Adds the warning message to the queue
     * 
     * @param string $message
     * @param boolean $clean TRUE removes all the previous warning messages
     */
    function addWarning($message, $clean=FALSE) {
        if ($clean || !($warnings = $this->getWarningMessages())) {
            $warnings = array();
        }
        $warnings[] = $message;
        $this->setWarningMessages($warnings);
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
    function getAllMessages($autoClean=TRUE) {
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
     * @param string $autoClean Empty current success message queue
     * @return string 
     */
    function getInfoHtml($autoClean=TRUE) {
        $html = '';
        $infos = $this->getInfoMessages();
        $autoClean && $this->unsetInfoMessages();
        if (!empty($infos)) {
            $html = '<ul class="infos">';
            foreach ($infos as $info) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_INFO] . '">' . $info . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

    /**
     * Get error messages as html
     * 
     * @param string $autoClean Empty current error message queue
     * @return string 
     */
    function getErrorHtml($autoClean=TRUE) {
        $html = '';
        $errors = $this->getErrorMessages();
        $autoClean && $this->unsetErrorMessages();
        if (!empty($errors)) {
            $html = '<ul class="errors">';
            foreach ($errors as $error) {
                $html = $html . '<li class="msg_' . $this->elClasses[self::TYPE_ERROR] . '">' . $error . '</li>';
            }
            $html.='</ul>';
        }
        return $html;
    }

    /**
     * Get warning messages as html
     * 
     * @param string $autoClean Empty current warning message queue
     * @return string 
     */
    function getWarningHtml($autoClean=TRUE) {
        $html = '';
        $warnings = $this->getWarningMessages();
        $autoClean && $this->unsetWarningMessages();
        if (!empty($warnings)) {
            $html = '<ul class="warnings">';
            foreach ($warnings as $warning) {
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
        $html = '';
        $infoHtml = $this->getInfoHtml($autoClean);
        $errorHtml = $this->getErrorHtml($autoClean);
        $warningHtml = $this->getWarningHtml($autoClean);
        $html = $infoHtml .
                $errorHtml .
                $warningHtml;
        return $html;
    }

}