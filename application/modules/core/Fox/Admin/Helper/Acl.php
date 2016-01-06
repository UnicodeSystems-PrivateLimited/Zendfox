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
 * Class Fox_Admin_Helper_Acl
 * 
 * @uses Uni_Core_Helper
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Helper_Acl extends Uni_Core_Helper {

    /**
     * Loads the accessible resources for the current user
     */
    public function loadResources() {
        $user = Fox::getModel('admin/session')->getLoginData();
        $roleModel = Fox::getModel('admin/role');
        $roleModel->load($user->getRoleId());
        Fox::getModel('admin/session')->setRole($roleModel->getId());
        Fox::getModel('admin/session')->setResourceIds(explode(',', $roleModel->getResourceIds()));
    }

    /**
     * Checks whether the resource is accessble or not
     * 
     * @param string $resource
     * @return boolean
     */
    public function isAllowed($resource) {
        $resources = Fox::getModel('admin/session')->getResourceIds();
        if (count($resources) == 1 && $resources[0] < 1) {
            if ($resources[0] == Fox_Admin_Model_Role::ROLE_ACCESS_ALL) {
                return TRUE;
            } else if ($resources[0] == Fox_Admin_Model_Role::ROLE_DENY_ALL) {
                return FALSE;
            }
        } else if (count($resources)) {
            $path = explode('/', $resource);
            if (count($path) == 3 && !$path[2]) {
                $resource = $resource . 'index';
            } else if (count($path) == 2) {
                if (!$path[1]) {
                    $resource.='index';
                }
                $resource = $resource . '/index';
            } else if (count($path) == 1) {
                $resource = $resource . '/index/index';
            }
            $resourceModel = Fox::getModel('admin/role/resource');
            $paths = explode('/', $resource);
            $modulePath = $paths[0];
            $controllerPath = $paths[0] . '/' . $paths[1];
            $actionPath = $paths[2];
            $actions = $resourceModel->getCollection("id IN(" . implode(',', $resources) . ") AND (resource='$modulePath' OR resource='$controllerPath') AND(action='-1' OR find_in_set('$actionPath',action))", 'id');
            $resourceModel->unsetData();
            if (count($actions) > 0) {
                return TRUE;
            }
        }
        return FALSE;
    }

}