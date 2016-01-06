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
 * Class Fox_Member_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_set_id` tinyint(4) unsigned NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `join_date` datetime NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=>Blocked 1=>Active 2=>not confirm',
  `member_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_set_id` (`attribute_set_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity') . "
  ADD CONSTRAINT `member_entity_ibfk_1` FOREIGN KEY (`attribute_set_id`) REFERENCES " . Fox::getTableName('eav_attribute_set') . " (`id`);
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity_date') . " (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `value` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id_2` (`attribute_id`,`entity_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity_date') . "
  ADD CONSTRAINT `member_entity_datetime_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_entity_datetime_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity_decimal') . " (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id_2` (`attribute_id`,`entity_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity_decimal') . "
  ADD CONSTRAINT `member_entity_decimal_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_entity_decimal_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity_int') . " (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id_2` (`attribute_id`,`entity_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity_int') . "
  ADD CONSTRAINT `member_entity_int_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_entity_int_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity_text') . " (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id_2` (`attribute_id`,`entity_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity_text') . "
  ADD CONSTRAINT `member_entity_text_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_entity_text_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_entity_varchar') . " (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attribute_id_2` (`attribute_id`,`entity_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE " . Fox::getTableName('member_entity_varchar') . "
  ADD CONSTRAINT `member_entity_varchar_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES " . Fox::getTableName('eav_attribute') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_entity_varchar_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_forget_password_entity') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE " . Fox::getTableName('member_forget_password_entity') . "  ADD CONSTRAINT `member_forget_password_entity_ibfk_1` FOREIGN KEY (`mem_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('member_verification_entity') . " (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mem_id` int(11) unsigned NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mem_id` (`mem_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE " . Fox::getTableName('member_verification_entity') . "
  ADD CONSTRAINT `member_forget_password_entity_ibfk_1` FOREIGN KEY (`mem_id`) REFERENCES " . Fox::getTableName('member_entity') . " (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->insertRecord(
        Fox::getTableName('eav_entity_type'), array(
    'id' => '1',
    'entity_type_code' => 'member'
));

$installer->insertRecord(
        Fox::getTableName('eav_attribute_set'), array(
    'id' => '1',
    'entity_type_id' => '1',
    'attribute_set_name' => 'Member'
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_group'), array(
    'id' => '1',
    'entity_type_id' => '1',
    'group_name' => 'Personal Details',
    'sort_order' => '0'
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_attribute_group'), array(
    'id' => '1',
    'attribute_set_id' => '1',
    'attribute_group_name' => 'Personal Details',
    'sort_order' => '0'
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '1',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'first_name',
    'data_type' => 'varchar',
    'input_type' => 'text',
    'frontend_label' => 'First Name',
    'validation' => '',
    'is_required' => '1',
    'is_editable' => '1',
    'is_backend_editable' => '1',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '2',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'last_name',
    'data_type' => 'varchar',
    'input_type' => 'text',
    'frontend_label' => 'Last Name',
    'validation' => '',
    'is_required' => '1',
    'is_editable' => '1',
    'is_backend_editable' => '1',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '3',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'email_id',
    'data_type' => 'varchar',
    'input_type' => 'text',
    'frontend_label' => 'Email ID',
    'validation' => 'email',
    'is_required' => '1',
    'is_editable' => '0',
    'is_backend_editable' => '0',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '4',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'password',
    'data_type' => 'varchar',
    'input_type' => 'password',
    'frontend_label' => 'Password',
    'validation' => '',
    'is_required' => '1',
    'is_editable' => '0',
    'is_backend_editable' => '0',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '5',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'confirm_password',
    'data_type' => 'varchar',
    'input_type' => 'password',
    'frontend_label' => 'Confirm Password',
    'validation' => '',
    'is_required' => '1',
    'is_editable' => '0',
    'is_backend_editable' => '0',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '6',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'member_image',
    'data_type' => 'varchar',
    'input_type' => 'file',
    'frontend_label' => 'Upload Image',
    'validation' => 'image',
    'is_required' => '0',
    'is_editable' => '0',
    'is_backend_editable' => '0',
    'is_visible_on_frontend' => '0',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);

$installer->insertRecord(
        Fox::getTableName('eav_entity_metadata_attribute'), array(
    'id' => '7',
    'entity_type_id' => '1',
    'group_id' => '1',
    'attribute_code' => 'id',
    'data_type' => 'int',
    'input_type' => 'hidden',
    'frontend_label' => '',
    'validation' => '',
    'is_required' => '0',
    'is_editable' => '0',
    'is_backend_editable' => '1',
    'is_visible_on_frontend' => '1',
    'default_value' => '',
    'position' => '0',
    'source_model' => ''
        )
);
?>
