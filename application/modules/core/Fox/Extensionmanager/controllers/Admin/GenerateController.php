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
 * Class Extensionmanager_Admin_GenerateController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Extensionmanager_Admin_GenerateController extends Fox_Admin_Controller_Action {

    /**
     * Get package session
     */
    protected function _getSession() {
        return Fox::getModel('extensionmanager/session');
    }

    /**
     * Get package
     */
    protected function getPackage() {
        return Fox::getModel("extensionmanager/generate/package");
    }

    /**
     * Index action
     * 
     * List all generated packages.
     */
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * New action
     */
    public function newAction() {
        $this->_getSession()->setFormData(NULL);
        $this->_forward("edit");
    }

    /**
     * Edit action
     */
    public function editAction() {
        $this->loadLayout();
        $view = Fox::getView('extensionmanager/admin/generate/add');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Generate Package');
        $this->renderLayout();
    }

    /**
     * Load action
     * 
     * Load an existing package to edit.
     */
    public function loadAction() {
        $packageName = str_replace(' ', '_', trim($this->getRequest()->getParam("package"))) . '-' . trim($this->getRequest()->getParam("version")) . '.xml';
        $package = $this->getPackage();
        try {
            $package->load($packageName);
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
        }
        $this->sendRedirect('*/*/edit');
    }

    /**
     * Save action
     * 
     * Saves package data to xml.
     */
    public function saveAction() {
        $data = $this->getRequest()->getPost();
        try {
            $package = $this->getPackage();
            $package->setData($data);
            if (!$package->hasErrors()) {
                if ($package->save()) {
                    Fox::getHelper('core/message')->addInfo("Package " . $package->getPackageName() . " saved successfully.");
                } else {
                    throw new Exception("Error while saving package.");
                }
            } else {
                throw new Exception(implode(",", $package->getErrors()));
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
        }
        $this->_getSession()->setFormData($data);
        if ($this->getRequest()->getParam('back')) {
            $this->sendRedirect('*/*/edit');
        } else {
            $this->sendRedirect('*/*/');
        }
    }

    /**
     * Generate Package action
     * 
     * Generates compressed package.
     */
    public function generatePackageAction() {
        try {
            if ($this->getPackage()->generate()) {
                Fox::getHelper('core/message')->addInfo("Package was generated successfully.");
            } else {
                Fox::getHelper('core/message')->addError("Problem while generating package.");
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
        }
        $this->sendRedirect('*/*/edit');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        try {
            if ($this->getPackage()->delete()) {
                Fox::getHelper('core/message')->addInfo("Package was deleted successfully.");
            } else {
                Fox::getHelper('core/message')->addError("Problem while deleting package.");
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Download action
     */
    public function downloadAction() {
        try {
            $package = $this->getPackage();
            $packageName = str_replace(' ', '_', trim($this->getRequest()->getParam("package"))) . '-' . trim($this->getRequest()->getParam("version")) . '.zip';
            $filename = $package->getGeneratedPkgCompressedDir() . DS . $packageName;
            if (!is_dir($filename) && file_exists($filename)) {
                header('Cache-Control: private; must-revalidate');
                header('Pragma: no-cache');
                header("Content-Type: application/zip");
                header('Content-Length: ' . filesize($filename));
                header('Content-Disposition: attachement; filename="' . $packageName . '"');
                header('Content-Transfer-Encoding: binary');
                readfile($filename);
            } else {
                Fox::getHelper('core/message')->addError("Package is not generated yet.");
                $this->sendRedirect('*/*/');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->addError($e->getMessage());
            $this->sendRedirect('*/*/');
        }
    }

    /**
     * Tree Data action
     * 
     * Gives xml data for directory tree
     */
    public function treeDataAction() {
        try {
            $cache = $this->getRequest()->getParam("cache") == "no" ? false : true;
            $result['content'] = $this->getPackage()->getSiteXml($cache);
            $result['success'] = true;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

}