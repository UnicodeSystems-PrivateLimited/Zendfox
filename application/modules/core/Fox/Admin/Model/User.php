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
 * Class Fox_Admin_Model_User
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Model_User extends Uni_Core_Model {
    const USER_STATUS_BLOCKED=0;
    const USER_STATUS_ENABLED=1;
    /**
     * Contains all users as array
     * 
     * @var array 
     */
    protected $userInfo;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'admin_user';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * 
     * @return array Active|Blocked 
     */
    public function getAllStatuses() {
        return array('' => 'Select Status', self::USER_STATUS_ENABLED => 'Active', self::USER_STATUS_BLOCKED => 'Blocked');
    }

    /**
     * Fetch all users as array
     * 
     * @return array contins user id as key and username as value
     */
    public function getUserInfo() {
        if (empty($this->userInfo)) {
            $data = $this->getCollection();
            $this->userInfo = array();
            $this->userInfo[''] = 'Users';
            foreach ($data as $row) {
                $this->userInfo[$row['id']] = $row['username'];
            }
        }
        return $this->userInfo;
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array
     * @throws Exception if username or email already exists 
     */
    protected function _preSave(array $data) {
        if ($this->isUserDuplicate($data['username'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['username'] . "' already exists.");
        } else if ($this->isEmailDuplicate($data['email'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['email'] . "' already exists.");
        }
        return $data;
    }

    /**
     * Checks whether the username already exists
     * 
     * @param string $username
     * @param int $id
     * @return boolean TRUE if username already exists 
     */
    private function isUserDuplicate($username, $id=0) {
        $collection = $this->getCollection(array('username' => $username, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection) ? TRUE : FALSE;
    }

    /**
     * Checks whether the email already exists
     * 
     * @param string $email
     * @param int $id
     * @return boolean TRUE if email already exists
     */
    private function isEmailDuplicate($email, $id=0) {
        $collection = $this->getCollection(array('email' => $email, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection) ? TRUE : FALSE;
    }

}