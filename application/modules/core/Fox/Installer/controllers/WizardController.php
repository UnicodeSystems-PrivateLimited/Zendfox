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
 * Class Installer_WizardController
 * 
 * @uses Fox_Installer_Controller_Action
 * @category    Fox
 * @package     Fox_Installer
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Installer_WizardController extends Fox_Installer_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        Fox::getModel('installer/session')->setCurrentStep(1);
        if ($this->getRequest()->isPost()) {
            $this->sendRedirect('*/wizard/locale');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Locale action
     */
    public function localeAction() {
        Fox::getModel('installer/session')->setCurrentStep(2);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            Fox::getModel('installer/session')->setLocale($data['locale']);
            Fox::getModel('installer/session')->setTimezone($data['timezone']);
            date_default_timezone_set($data['timezone']);
            $this->sendRedirect('*/wizard/configure', array('locale' => urlencode($data['locale']), 'timezone' => urlencode($data['timezone'])));
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Database configuration action
     */
    public function configureAction() {
        $locale = $this->getRequest()->getParam('locale', FALSE);
        $timezone = $this->getRequest()->getParam('timezone', FALSE);
        if (!$locale || !$timezone) {
            $this->sendRedirect('*/*/locale');
        }
        Fox::getModel('installer/session')->setCurrentStep(3);
        $this->loadLayout();
        $error = FALSE;
        $data = array();
        try {
            $configDirectory = CONFIG_DIR;
            $uploadsDirectory = UPLOAD_DIR;
            $varDirectory = VAR_DIR;
            $sessionDirectory = SESSION_DIR;
            $extensionDirectory = EXTENSION_DIR;
            if (!is_writable($configDirectory)) {
                Fox::getHelper('core/message')->addError('Path "' . realpath($configDirectory) . '" must be word writable.');
                $error = TRUE;
            }
            if (!is_writable($uploadsDirectory)) {
                Fox::getHelper('core/message')->addError('Path "' . realpath($uploadsDirectory) . '" must be word writable.');
                $error = TRUE;
            }
            if (!is_writable($varDirectory)) {
                Fox::getHelper('core/message')->addError('Path "' . realpath($varDirectory) . '" must be word writable.');
                $error = TRUE;
            }
            if (!is_writable($extensionDirectory)) {
                Fox::getHelper('core/message')->addError('Path "' . realpath($extensionDirectory) . '" must be word writable.');
                $error = TRUE;
            }
            $errors = Uni_Core_Installer::checkServerCompatiblity();
            if (!empty($errors)) {
                foreach ($errors as $err) {
                    Fox::getHelper('core/message')->addError($err);
                }
                $error = TRUE;
            }
            if (!$error && !file_exists($sessionDirectory)) {
                if (!@mkdir($sessionDirectory, 0777) || !is_writable($sessionDirectory)) {
                    Fox::getHelper('core/message')->addError('Path "' . realpath($sessionDirectory) . '" must be word writable.');
                    $error = TRUE;
                }
            }
            if ($error) {
                $this->getViewByKey('configure')->error = TRUE;
                throw new Exception('');
            }
            Fox::getModel('installer/session')->unsetAllData();
            Fox::getModel('installer/session')->setCurrentStep(3);
            Fox::getModel('installer/session')->setLocale(urldecode($locale));
            Fox::getModel('installer/session')->setTimezone(urldecode($timezone));
        } catch (Exception $e) {
            
        }
        $this->renderLayout();
    }

    /**
     * Database configuration post action
     */
    public function configurePostAction() {
        $error = FALSE;
        $data = array();
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                Fox::getModel('installer/session')->setConfiguration($data);
                $hostName = $data['db_host'];
                $dbName = $data['db_name'];
                $userName = $data['db_username'];
                $password = $data['db_password'];
                $dbAdapter = Zend_Db::factory('Pdo_Mysql', array(
                            'host' => $hostName,
                            'username' => $userName,
                            'password' => $password,
                            'dbname' => $dbName
                        ));
                if (!$dbAdapter->getConnection()) {
                    throw new Exception('Databse connection failed');
                }
                Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
                Uni_Fox::setDatabaseAdapter($dbAdapter);
                Uni_Core_Installer::runInstaller();
                $this->sendRedirect('*/wizard/administrator');
                return;
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        $this->sendRedirect('*/wizard/configure', array('locale' => urlencode(Fox::getModel('installer/session')->getLocale()), 'timezone' => urlencode(Fox::getModel('installer/session')->getTimezone())));
    }

    /**
     * Admin panel configuration action
     */
    public function administratorAction() {
        Fox::getModel('installer/session')->setCurrentStep(4);
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                Fox::getModel('installer/session')->setBackend($data);
                if (!$data['password'] || $data['password'] != $data['confirm_password']) {
                    throw new Exception('Password and Confirm Password do not match');
                }
                Uni_Core_Installer::finishSetup();
                $this->sendRedirect('*/*/finish');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Finish setup action
     */
    public function finishAction() {
        if (Uni_Core_Installer::isInstalled()) {
            Fox::getModel('installer/session')->setCurrentStep(5);
            $this->loadLayout();
            $this->renderLayout();
            Fox::getModel('installer/session')->unsetAllData();
        } else {
            $this->sendRedirect('*/*');
        }
    }

}