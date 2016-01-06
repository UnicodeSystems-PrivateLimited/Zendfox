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
 * Class Admin_System_CacheController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_System_CacheController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('admin/system/cache');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Cache Management');
        $this->renderLayout();
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('admin/system/cache/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

    /**
     * Bulk status update action
     */
    public function groupStatusAction() {
        try {
            $model = Fox::getModel('core/cache');
            $codes = $this->getGroupActionIds($model);
            $totalIds = count($codes);
            $updateType = '';
            $dependentParams = $this->getRequest()->getParam('dependents', array());
            if (isset($dependentParams['status'])) {
                if ($dependentParams['status'] == Fox_Core_Model_Cache::STATUS_CLEAR) {
                    $updateType = 'cleared';
                    if (in_array(Fox_Core_Model_Cache::CACHE_CODE_PREFERENCE, $codes)) {
                        Uni_Core_Preferences::loadPreferences(TRUE);
                        Fox::initializePreferences();
                    }
                    if (in_array(Fox_Core_Model_Cache::CACHE_CODE_LAYOUT, $codes)) {
                        Uni_Core_CacheManager::clearLayoutCache();
                    }
                } else {
                    if ($dependentParams['status'] == Fox_Core_Model_Cache::STATUS_ENABLED) {
                        $updateType = 'enabled';
                    } else if ($dependentParams['status'] == Fox_Core_Model_Cache::STATUS_DISABLED) {
                        $updateType = 'disabled';
                    }
                    $cacheCodes = '\'' . implode('\',\'', $codes) . '\'';
                    $model->update(array('status' => $dependentParams['status']), 'cache_code IN (' . $cacheCodes . ')');
                }
                Uni_Core_CacheManager::createCacheSettings();
                Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' cache(s) successfully ' . $updateType . '.');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}