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
 * Class Fox_Admin_Controller_Action
 * 
 * @uses Uni_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Controller_Action extends Uni_Controller_Action {
    const LEFT='left';
    const RIGHT='right';
    const CONTENT='content';
    const BREADCRUMB='breadcrumb';
    /**
     * Application Mode
     *
     * @var string
     */
    protected $appMode = 'admin';

    /**
     * Controller predispatch method
     * 
     * Called before action method
     */
    function preDispatch() {
        parent::preDispatch();
        if (!Fox::getModel('admin/session')->getLoginData()) {
            if (!('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'index' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'change-password' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'forget-password' === strtolower($this->getRequest()->getActionName())) && !('admin' === strtolower($this->getRequest()->getModuleName()) && 'index' === strtolower($this->getRequest()->getControllerName()) && 'logout' === strtolower($this->getRequest()->getActionName()))) {
                setcookie('requested_uri', $this->getRequestUriAfterBaseUrl(), time() + 3600 * 24, $this->getFrontController()->getBaseUrl());
                if ($this->getRequest()->getParam('isAjax', FALSE)) {
                    echo '<script type="text/javascript">window.location.reload();</script>';
                    exit;
                } else {
                    $this->sendRedirect('admin');
                }
            }
        } else if (isset($_COOKIE['requested_uri'])) {
            $redirect = $_COOKIE['requested_uri'];
            $path = explode('/', $redirect);
            if (count($path) == 3 && !$path[2]) {
                $redirect = $redirect . 'index';
            } else if (count($path) == 2) {
                if (!$path[1]) {
                    $redirect.='index';
                }
                $redirect = $redirect . '/index';
            } else if (count($path) == 1) {
                $redirect = $redirect . '/index/index';
            }
            $paths = explode('/', $redirect);
            $moduleName = $paths[0];
            $controllerName = strtolower($paths[1]);
            $actionName = strtolower($paths[2]);
            setcookie('requested_uri', '', time() - 3600 * 24, $this->getFrontController()->getBaseUrl());
            if (!('admin' === $moduleName && 'access-denied' === $controllerName && $actionName === 'index') && !('admin' === $moduleName && 'index' === $controllerName && 'logout' === $actionName) && !Fox::getHelper('admin/acl')->isAllowed($moduleName . '/' . $controllerName . '/' . $actionName)) {
                if ($this->getRequest()->getParam('isAjax', FALSE)) {
                    echo '<script type="text/javascript">window.location.href=' . Fox::getUrl('*/access-denied/') . ';</script>';
                    exit;
                } else {
                    $this->sendRedirect('*/access-denied/');
                }
            } else {
                $this->sendRedirect($redirect);
            }
        } else {
            $moduleName = strtolower($this->getRequest()->getModuleName());
            $controllerName = strtolower($this->getRequest()->getControllerName());
            $actionName = strtolower($this->getRequest()->getActionName());
            if (!('admin' === $moduleName && 'access-denied' === $controllerName && $actionName === 'index') && !('admin' === $moduleName && 'index' === $controllerName && 'logout' === $actionName) && !Fox::getHelper('admin/acl')->isAllowed($moduleName . '/' . $controllerName . '/' . $actionName)) {
                if ($this->getRequest()->getParam('isAjax', FALSE)) {
                    echo '<script type="text/javascript">window.location.href=\'' . Fox::getUrl('*/access-denied/') . '\';</script>';
                    exit;
                } else {
                    $this->sendRedirect('*/access-denied/');
                }
            }
        }
    }

    /**
     * Adds the contents to the left bar
     * 
     * @param Uni_Core_Admin_View $view 
     */
    public function _addLeft(Uni_Core_Admin_View $view) {
        if (($lView = $this->getViewByKey(self::LEFT))) {
            $lView->addChild('', $view);
        }
    }

    /**
     * Adds the contents to the right bar
     * @param Uni_Core_Admin_View $view 
     */
    public function _addRight(Uni_Core_Admin_View $view) {
        if (($rView = $this->getViewByKey(self::RIGHT))) {
            $rView->addChild('', $view);
        }
    }

    /**
     * Adds the contents to the content section
     * 
     * @param Uni_Core_Admin_View $view 
     */
    public function _addContent(Uni_Core_Admin_View $view) {
        if (($cView = $this->getViewByKey(self::CONTENT))) {
            $cView->addChild('', $view);
        }
    }

    /**
     * Adds the contents to the breadcumb
     * 
     * @param Uni_Core_Admin_View $view 
     */
    public function _addBreadcrumb(Uni_Core_Admin_View $view) {
        if (($brView = $this->getViewByKey(self::BREADCRUMB))) {
            $brView->addChild('', $view);
        }
    }

    /**
     * Fetches the group action values
     * 
     * @param Uni_Core_Model $model Object of Model to which the group action applies
     * @param string $loadField [Optional]<p>Primary Key field of the Model</p>
     * @return mixed Array|NULL
     * @throws Exception if Model Object is NULL 
     */
    protected function getGroupActionIds(Uni_Core_Model $model, $loadField=NULL) {
        $condition = '';
        $selectType = $this->getRequest()->getParam('selected', NULL);
        $ids = $this->getRequest()->getParam('extra', NULL);
        $filters = $this->getRequest()->getParam('filters', FALSE);
        $columns = $this->getRequest()->getParam('colTypes', FALSE);
        if (!isset($selectType) || !isset($ids)) {
            throw new Exception('Unsufficient Parameters sent. Unable to perform group action');
        }
        if (!$model) {
            throw new Exception('Invalid model object was given');
        }
        if ($selectType > 0) {
            $loadField = $loadField != NULL ? $loadField : $model->getPrimaryField();
            if ($columns) {
                if (get_magic_quotes_gpc()) {
                    $columns = Zend_Json_Decoder::decode(stripslashes($columns));
                } else {
                    $columns = Zend_Json_Decoder::decode($columns);
                }
            }
            if (!empty($filters)) {
                foreach ($filters as $key => $value) {
                    if (is_array($value)) {
                        if (isset($value['from']) && $value['from']) {
                            $condition.= ( $condition ? ' AND ' : '') . "$key>='" . $value['from'] . "'";
                        }
                        if (isset($value['to']) && $value['to']) {
                            $condition.= ( $condition ? ' AND ' : '') . "$key<='" . $value['to'] . "'";
                        }
                    } else if (isset($columns) && isset($columns[$key]) && $columns[$key] == Fox_Core_View_Admin_Table::TYPE_OPTIONS) {
                        $condition.= ( $condition ? ' AND ' : '') . "$key = '$value'";
                    } else {
                        $condition.= ( $condition ? ' AND ' : '') . "$key LIKE '%$value%'";
                    }
                }
            }
            if (!empty($ids)) {
                $condition.= ( $condition ? ' AND ' : '') . $loadField . ' NOT IN(\'' . implode('\',\'', $ids) . '\')';
            }
            $ids = array();
            $collection = $model->getCollection($condition, array($loadField));
            foreach ($collection as $row) {
                $ids[] = $row[$loadField];
            }
        }
        return $ids;
    }

}
