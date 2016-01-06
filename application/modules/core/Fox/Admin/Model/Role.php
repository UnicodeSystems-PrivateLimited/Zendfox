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
 * Class Fox_Admin_Model_Role
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Model_Role extends Uni_Core_Model {
    const ROLE_ACCESS_CUSTOM=1;
    const ROLE_ACCESS_ALL=-1;
    const ROLE_DENY_ALL=0;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'admin_role';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get user roles
     * 
     * @return array 
     */
    public function getUserRole() {
        $data = $this->getCollection();
        $roles = array();
        $roles[''] = 'Select User Role';
        foreach ($data as $row) {
            $roles[$row['id']] = $row['name'];
        }
        return $roles;
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if role name already exists
     */
    protected function _preSave(array $data) {
        if ($this->isRoleDuplicate($data['name'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['name'] . "' already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the role name already exists
     * 
     * @param string $name role name
     * @param int $id role id
     * @return boolean TRUE if role name already exists
     */
    private function isRoleDuplicate($name, $id=0) {
        $collection = $this->getCollection(array('name' => $name, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection) ? TRUE : FALSE;
    }

    /**
     * Get admin role model object
     * 
     * @param int $roleId
     * @return Fox_Admin_Model_Role 
     */
    public function getRole($roleId) {
        return $this->load($roleId);
    }

    /**
     *
     * @return array Custom|All 
     */
    public function getRoleAccess() {
        return array(self::ROLE_ACCESS_CUSTOM => 'Custom', self::ROLE_ACCESS_ALL => 'All');
    }

}