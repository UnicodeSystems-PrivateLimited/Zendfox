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
 * Class Extensionmanager_Admin_ConnectController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Extensionmanager_Admin_ConnectController extends Fox_Admin_Controller_Action {

    /**
     *
     * @var Fox_Extensionmanager_Model_Package 
     */
    protected $package;
    
    /**
     * Controller predispatch method
     * 
     * Called before action method
     */
    function preDispatch() {
        if (!Fox::getModel('admin/session')->getLoginData()) {
            if (!('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'index' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'change-password' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'forget-password' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'logout' === strtolower($this->getRequest()->getActionName()))) {
                setcookie('requested_uri', $this->getRequestUriAfterBaseUrl(), time() + 3600 * 24, $this->getFrontController()->getBaseUrl());
                if ($this->getRequest()->getParam('isAjax')) {
                    $result['html'] = 'Session time out.';
                    $result['success'] = false;
                    $result['redirect'] = 'document.URL';
                    echo Zend_Json::encode($result); exit;
                } else {
                    $this->sendRedirect('admin');
                }
            }
        }
        parent::preDispatch();
    }
    /**
     * Check whether request is ajax and post or not
     * 
     * @return boolean
     */
    protected function isAjax() {
        if ($this->getRequest()->getParam('isAjax') && $this->getRequest()->isPost()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get package session
     */
    protected function _getSession() {
        return Fox::getModel("extensionmanager/session");
    }

    /**
     * Get package
     * 
     * @return Fox_Extensionmanager_Model_Package 
     */
    protected function getPackage() {
        if (!$this->package) {
            $package = $this->_getSession()->getPackage();
            if ($package && !$package->hasErrors()) {
                /* looking for package in session without any error */
                $this->package = $this->_getSession()->getPackage();
            } else {
                $this->package = Fox::getModel('extensionmanager/package');
            }
        }
        return $this->package;
    }

    /**
     * Error action
     */
    public function errorAction() {
        $result['html'] = 'Invalid package key';
        $result['success'] = false;
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Index action
     */
    public function indexAction() {
        $this->_getSession()->unsetPackage();
        $this->package = null;
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Prepare package action
     */
    public function preparePackageAction() {
        $result = array('html'=>'','success'=> false);
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $packageKey = $this->getRequest()->getPost("pack_key");
        $package = $this->getPackage()->setKey($packageKey);
        if ($package->hasErrors()) {//checking for package errors
            $this->_forward('error');
            return;
        }
        $package->_prepareInfo();
        if ($package->hasErrors()) {//checking again for package errors
            $this->_forward('error');
            return;
        }
        $result['html'] = $this->renderDependency();
        $result['success'] = true;
        $result['updateSection'] = 'proceedToInstall';
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Download package action
     */
    public function downloadPackageAction() {
        $result = array();
        $result['html'] = '';
        $result['success'] = false;
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $package = $this->getPackage();
        if ($package->getPackageInfo()) {
            $package->downloadPackage();
            if ($package->hasErrors()) {
                $this->_forward('error');
                return;
            }
            $result['success'] = true;
            $result['html'] = false;
            $result['status'] = $package->getStatusText();
            //package successfully downloaded
        } else {
            $this->_forward('error');
            return;
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Install package action
     */
    public function installPackageAction() {
        $result['success'] = false;
        $package = $this->getPackage();
        try {
            $package->installPackage();
            $result['success'] = true;
            $result['status'] = $package->getStatusText();
            $package->reset();
            $result["update"] = $this->_getUpdatedSection();
        } catch (Exception $e) {
            $result['exception'] = $e->getMessage();
            $result['status'] = $package->getStatusText();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Render dependency of package
     * 
     * @return string 
     */
    protected function renderDependency() {
        $view = Fox::getView('extensionmanager/admin/dependency');
        $view->setTemplate('extensionmanager/dependency');
        $view->setViewKey('pkgdependency');
        $view->setViewOptions($this->package->getPackageInfo());
        return $view->renderView();
    }

    /**
     * Reinstall package action
     * 
     * @throws Exception
     */
    public function reinstallPackageAction() {
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $result['success'] = false;
        $package = $this->getPackage();
        $key = $this->getRequest()->getPost("key");
        try {
            if (!$key) {
                throw new Exception("Invalid Package Key");
            }
            $package->reinstallPackage(Fox::getHelper("extensionmanager/data")->decrypt($key));
            $result['status'] = $package->getStatusText();
            $result['success'] = true;
            $result["update"] = $this->_getUpdatedSection();
        } catch (Exception $e) {
            $result["html"] = $e->getMessage();
            $result['status'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Uninstall package action
     * 
     * @throws Exception
     */
    public function uninstallPackageAction() {
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $result['success'] = false;
        $package = $this->getPackage();
        $key = $this->getRequest()->getPost("key");
        try {
            if (!$key) {
                throw new Exception("Invalid Package Key");
            }
            $package->uninstallPackage(Fox::getHelper("extensionmanager/data")->decrypt($key));
            $result['status'] = $package->getStatusText();
            $result['success'] = true;
            $result["update"] = $this->_getUpdatedSection();
        } catch (Exception $e) {
            $result["html"] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Upgrade package action
     */
    public function upgradePackageAction(){
        $result = array('html'=>'','success'=> false);
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $package = $this->getPackage();
        $conf = $package->getConfig()->getConfiguration();
        $packageKey = $this->getRequest()->getPost("key");
        $packageKey = $conf->fox_market.$conf->downloader->install_channel_action.'/key/'.Fox::getHelper("extensionmanager/data")->decrypt($packageKey);
        
        $package->setKey($packageKey);
        if ($package->hasErrors()) {//checking for package errors
            $this->_forward('error');
            return;
        }
        $package->_prepareInfo();
        if ($package->hasErrors()) {//checking again for package errors
            $this->_forward('error');
            return;
        }
        $result['success'] = true;
        $result['statue'] = $package->getStatus();
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * Get updated section
     * 
     * @return array 
     */
    protected function _getUpdatedSection() {
        $view = Fox::getView("extensionmanager/admin/packages", "generated-pkgs");
        $view->setTemplate("extensionmanager/installed-pkgs");
        return array("section" => "installed-packages", "html" => $view->renderView());
    }

    /**
     * Finish Upgrade Action
     */
    public function finishUpgradeAction(){
        $result = array('html'=>'','success'=> false);
        if (!$this->isAjax()) {
            $this->_forward('error');
            return;
        }
        $package = $this->getPackage();
        $config = $package->getConfig();
        $packageKey = Fox::getHelper("extensionmanager/data")->decrypt($this->getRequest()->getPost("key"));
        $filePath = $config->getInstalledPkgPath().DS.$packageKey.'.xml';
        if(!is_dir($filePath) && file_exists($filePath)){
            @unlink($filePath);
        }
        $result["success"] = true;
        $result["state"] = "Upgrading Done";
        $result["update"] = $this->_getUpdatedSection();
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }
}