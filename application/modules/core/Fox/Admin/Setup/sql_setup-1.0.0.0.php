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
 * Class Fox_Admin_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_analytics') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_value` date NOT NULL,
  `total_results` int(11) NOT NULL,
  `page_views` int(11) NOT NULL,
  `total_visits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_role') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `resource_ids` text NOT NULL COMMENT 'Comma Seperated Resource Ids',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

$installer->insertRecord(Fox::getTableName('admin_role'), array(
    'id' => '1',
    'name' => 'Administrator',
    'resource_ids' => '-1',
));

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_user') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0=>Blocked 1=>Active',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
                
ALTER TABLE " . Fox::getTableName('admin_user') . "
  ADD CONSTRAINT `admin_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES " . Fox::getTableName('admin_role') . " (`id`);
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_forget_password_entity') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE " . Fox::getTableName('`admin_forget_password_entity`') . "
  ADD CONSTRAINT `admin_forget_password_entity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES " . Fox::getTableName('admin_user') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
?>
