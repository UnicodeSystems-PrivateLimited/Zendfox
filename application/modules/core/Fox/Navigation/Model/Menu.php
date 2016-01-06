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
 * Class Fox_Navigation_Model_Menu
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_Model_Menu extends Uni_Core_Model {
    const STATUS_ENABLED=1;
    const STATUS_DISABLED=0;
    const TARGET_WINDOW_NEW='_blank';
    const TARGET_WINDOW_SAME='_self';
    /**
     * Menu group table name
     * 
     * @var string
     */
    private $_menuGroup = 'navigation_menu_group';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'navigation_menu_item';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get status options
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

    /**
     * Get menu link target window options
     * 
     * @return array
     */
    public function getAllTargetWindows() {
        return array('' => '', self::TARGET_WINDOW_NEW => 'New Window', self::TARGET_WINDOW_SAME => 'Same Window');
    }

    /**
     * Get menu items
     * 
     * @param int $id
     * @return array
     */
    public function getMenuItemOptions($id=0) {
        $condition = NULL;
        if ($id) {
            $condition = "id!=$id";
        }
        $collection = $this->getCollection($condition);
        $menus = array('' => '');
        foreach ($collection as $menu) {
            $menus[$menu['id']] = $menu['title'];
        }
        return $menus;
    }

    /**
     * Get menu items in the menu group
     * 
     * @param string $key
     * @return array 
     */
    public function getMenuItemsByGroup($key) {
        $select = $this->getAdapter()->select()->from(array('mi' => $this->_name), '*')
                ->where('mi.status=' . self::STATUS_ENABLED)
                ->join(array('mg' => Fox::getTableName($this->_menuGroup)), 'mg.key = "' . addslashes($key) . '" AND find_in_set(mg.id, mi.menu_group)', 'mg.id')
                ->order('mi.sort_order ASC');
        return $this->getAdapter()->fetchAll($select);
    }

}