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
 * Class Fox_Eav_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_entity_type') . " (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_code` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_attribute_set') . " (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` tinyint(4) unsigned NOT NULL,
  `attribute_set_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('eav_attribute_set') . "
  ADD CONSTRAINT `eav_attribute_set_ibfk_1` FOREIGN KEY (`entity_type_id`) REFERENCES " . Fox::getTableName('eav_entity_type') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_attribute') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` tinyint(4) unsigned NOT NULL,
  `attribute_code` varchar(150) NOT NULL,
  `data_type` varchar(30) NOT NULL,
  `input_type` varchar(30) NOT NULL,
  `frontend_label` varchar(150) NOT NULL,
  `validation` varchar(100) DEFAULT NULL,
  `is_editable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `is_backend_editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=>No, 1=>Yes',
  `is_visible_on_frontend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `is_required` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `is_user_defined` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>System 1=>User',
  `default_value` text,
  `is_unique` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `position` int(11) NOT NULL DEFAULT '0',
  `source_model` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_code` (`attribute_code`),
  KEY `entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('eav_attribute') . "
  ADD CONSTRAINT `eav_attribute_ibfk_1` FOREIGN KEY (`entity_type_id`) REFERENCES " . Fox::getTableName('eav_entity_type') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_attribute_group') . " (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_set_id` tinyint(4) unsigned NOT NULL,
  `attribute_group_name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attribute_set_id` (`attribute_set_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('eav_attribute_group') . "
  ADD CONSTRAINT `eav_attribute_group_ibfk_1` FOREIGN KEY (`attribute_set_id`) REFERENCES " . Fox::getTableName('eav_attribute_set') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_attribute_option') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  PRIMARY KEY (`id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('eav_attribute_option') . "
  ADD CONSTRAINT `eav_attribute_option_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_entity_metadata_group') . " (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` tinyint(4) unsigned NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE " . Fox::getTableName('eav_entity_metadata_group') . "
  ADD CONSTRAINT `eav_entity_metadata_group_ibfk_1` FOREIGN KEY (`entity_type_id`) REFERENCES " . Fox::getTableName('eav_entity_type') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_entity_metadata_attribute') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` tinyint(4) unsigned NOT NULL,
  `group_id` tinyint(4) unsigned NOT NULL,
  `attribute_code` varchar(150) NOT NULL,
  `data_type` varchar(30) NOT NULL,
  `input_type` varchar(30) NOT NULL,
  `frontend_label` varchar(150) NOT NULL,
  `validation` varchar(100) DEFAULT NULL,
  `is_required` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `is_editable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `is_backend_editable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No, 1=>Yes',
  `is_visible_on_frontend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `default_value` text,
  `position` int(11) NOT NULL DEFAULT '0',
  `source_model` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('eav_entity_metadata_attribute') . "
  ADD CONSTRAINT `eav_entity_metadata_attribute_ibfk_1` FOREIGN KEY (`entity_type_id`) REFERENCES " . Fox::getTableName('eav_entity_type') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eav_entity_metadata_attribute_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES " . Fox::getTableName('eav_entity_metadata_group') . " (`id`);
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('eav_entity_attribute') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_set_id` tinyint(4) unsigned NOT NULL,
  `attribute_group_id` smallint(5) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attribute_set_id` (`attribute_set_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `attribute_group_id` (`attribute_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE " . Fox::getTableName('eav_entity_attribute') . "
  ADD CONSTRAINT `eav_entity_attribute_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eav_entity_attribute_ibfk_2` FOREIGN KEY (`attribute_set_id`) REFERENCES " . Fox::getTableName('eav_attribute_set') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eav_entity_attribute_ibfk_3` FOREIGN KEY (`attribute_group_id`) REFERENCES " . Fox::getTableName('eav_attribute_group') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
?>
