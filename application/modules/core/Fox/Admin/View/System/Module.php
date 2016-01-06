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
 * Class Fox_Admin_View_System_Module
 * 
 * @uses Fox_Core_View_Admin_Form_Container
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Module extends Fox_Core_View_Admin_Form_Container {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('admin/system/module/list');
        $this->setId('moduleList');
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        
    }

    /**
     * Get all modules
     * 
     * @return array
     */
    public function getModuleList() {
        return Uni_Core_ModuleManager::getModules();
    }

    /**
     * Get installed modules
     * 
     * @return Uni_Core_Model_Collection
     */
    public function getInstalledModulesCollection() {
        $moduleModel = Fox::getModel('core/module');
        return $moduleModel->getInstalledModules();
    }
    
    /**
     * Update modules status
     * 
     * @param array $modules module data
     */
    public function updateModulesStatus($modules) {
        Uni_Core_ModuleManager::updateModuleStatus($modules);
    }
    
    /**
     * Delete Modules
     * 
     * @param array $modules Name of modules that needs to be deleted are passed as array values
     */
    public function deleteModules($modules){
        foreach ($modules as $moduleName) {
            $module=Fox::getModel('core/module');
            $module->load($moduleName);
            $module->delete();
        }
    }

}