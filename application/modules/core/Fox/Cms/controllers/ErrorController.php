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
 * Class ErrorController
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class ErrorController extends Fox_Core_Controller_Action {

    /**
     * Error action
     * 
     * @return void
     */
    public function errorAction() {        
        $errors = $this->_getParam('error_handler');
        if (!$errors) {
            $message = 'You have reached the error page.';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $message = 'Page not found';
                $this->sendForward('*/index/index');
                return;
            default:
                $this->loadLayout();
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $message = 'Application error';
                break;
        }

        // Log exception, if logger available
        if (($log = $this->getLog())) {
            $log->crit($message, $errors->exception);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $exception = $errors->exception;
        }

        // create log file        
        $path = VAR_DIR . DIRECTORY_SEPARATOR . 'error';
        if (!file_exists($path)) {
            if (!@mkdir($path, 0777, TRUE)) {
                throw new Exception('var directory was not found.');
            }
        }
        $fName = 'L_exp_' . time();
        $filename = $path . DIRECTORY_SEPARATOR . $fName;

        $fp = fopen($filename, 'w');
        if ($fp) {
            fwrite($fp, $exception->getMessage() . "\n" . $exception->getTraceAsString());
            fclose($fp);
        }
        if (($excView = $this->getViewByKey('exception'))) {
            $excView->setErrorFile($fName, $filename);
        }
        $this->renderLayout();
    }

    /**
     * Get log resource
     * 
     * @return mixed
     */
    public function getLog() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

}

