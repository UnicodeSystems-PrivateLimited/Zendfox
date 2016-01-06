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
 * Class Fox_Navigation_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('navigation_menu_group') . " (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=>Disable 1=>Enable',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

$installer->insertRecord(
        Fox::getTableName('navigation_menu_group'), array(
    'id' => '1',
    'name' => 'Header Menu',
    'sort_order' => '0',
    'key' => 'HEADER',
    'description' => 'Header Menu',
    'status' => '1'
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_group'), array(
    'id' => '2',
    'name' => 'Left Menu',
    'sort_order' => '1',
    'key' => 'LEFT',
    'description' => 'Member Menus',
    'status' => '1'
        )
);

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('navigation_menu_item') . " (
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `open_window` varchar(20) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=>Disabled 1=>Enabled',
  `menu_group` varchar(255) NOT NULL,
  `style_class` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '1',
    'title' => 'Home',
    'link' => '',
    'open_window' => '_self',
    'sort_order' => '1',
    'status' => '1',
    'menu_group' => '1',
    'style_class' => 'home',
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '2',
    'title' => 'About Us',
    'link' => 'about',
    'open_window' => '_self',
    'sort_order' => '2',
    'status' => '1',
    'menu_group' => '1',
    'style_class' => '',
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '3',
    'title' => 'Contact Us',
    'link' => 'contact',
    'open_window' => '_self',
    'sort_order' => '3',
    'status' => '1',
    'menu_group' => '1',
    'style_class' => '',
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '4',
    'title' => 'View Profile',
    'link' => 'member/account/profile',
    'open_window' => '_self',
    'sort_order' => '1',
    'status' => '1',
    'menu_group' => '2',
    'style_class' => '',
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '5',
    'title' => 'Edit Profile',
    'link' => 'member/account/edit-profile',
    'open_window' => '_self',
    'sort_order' => '2',
    'status' => '1',
    'menu_group' => '2',
    'style_class' => '',
        )
);

$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '6',
    'title' => 'Change Password',
    'link' => 'member/account/change-password',
    'open_window' => '_self',
    'sort_order' => '3',
    'status' => '1',
    'menu_group' => '2',
    'style_class' => '',
        )
);
$installer->insertRecord(
        Fox::getTableName('navigation_menu_item'), array(
    'id' => '7',
    'title' => 'News',
    'link' => 'news',
    'open_window' => '_self',
    'sort_order' => '4',
    'status' => '1',
    'menu_group' => '1',
    'style_class' => '',
        )
);
?>
