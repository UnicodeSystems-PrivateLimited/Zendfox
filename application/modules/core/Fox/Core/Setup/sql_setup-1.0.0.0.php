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
 * Class Fox_Core_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;
$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('core_module') . " (
  `name` varchar(80) NOT NULL,
  `package` varchar(80) NOT NULL,
  `namespace` varchar(80) NOT NULL,
  `version` varchar(80) NOT NULL,
  `sql_version` varchar(80) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=>Disabled 1=>Enabled',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");
$installer->runSetup("                
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('core_preferences') . " (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
$installer->runSetup("                
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('core_cache') . " (
  `cache_type` varchar(100) NOT NULL,
  `cache_code` varchar(25) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=>Disabled 1=>Enabled',
  PRIMARY KEY (`cache_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");
$installer->insertRecord(Fox::getTableName('core_cache'), array(
    'cache_type' => 'Layouts',
    'cache_code' => Fox_Core_Model_Cache::CACHE_CODE_LAYOUT,
    'description' => 'Layout Files',
    'status' => '0'
));
$installer->insertRecord(Fox::getTableName('core_cache'), array(
    'cache_type' => 'Preferences',
    'cache_code' => Fox_Core_Model_Cache::CACHE_CODE_PREFERENCE,
    'description' => 'Preferences Collection Data',
    'status' => '0'
));
$installer->runSetup("                
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('core_session') . " (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");
$installer->runSetup("                
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('core_email_template') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_date` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '1',
    'name' => 'Account Registration Confirmation Link',
    'subject' => 'Account Registration Confirmation Link',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a href="{{url}}"> <img style="margin-bottom: 0px;" src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Your e-mail {{email}} must be confirmed before using it to log in to our store.</p><p style="font-size: 12px; line-height: 16px; margin: 0 0 8px 0;">To confirm the e-mail and instantly log in, please, use <a style="color: #1e7ec8;" href="{{link}}">this confirmation link</a>. This link is valid only once.</p><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0 0 16px 0; padding: 13px 18px; background: #eaf3f6;">Use the following values when prompted to log in:<br /> <strong>E-mail:</strong> {{email}}<br /> <strong>Password:</strong>{{password}}</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '2',
    'name' => 'Account Registration Welcome',
    'subject' => 'Account Registration Welcome',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a href="{{webisteName}}"><img style="margin-bottom: 0px;" src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Welcome to {{websiteName}}. To log in just click <a style="color: #1e7ec8;" href="{{url=member/account/login}}">Login</a> and then enter your e-mail address and password.</p><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">Use the following when prompted to log in: <br /> <strong>E-mail</strong>: {{email}} <br /> <strong>Password</strong>: {{password}}</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '3',
    'name' => 'Member Forgot Password',
    'subject' => 'Forgot Account Password',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="font-size: 12px; line-height: 16px; margin: 0;">To change your password <a style="color: #1e7ec8;" href="{{link}}">Click Here</a></p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '4',
    'name' => 'Account Registration Confirmed',
    'subject' => 'Account Registration Confirmed',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">Welcome to {{websiteName}}. To log in to our site just click <a style="color: #1e7ec8;" href="{{url=member/account}}">Login</a> and provide your credentials</p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again, <br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '5',
    'name' => 'Account Password Changed',
    'subject' => 'Account Password Changed',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a href="{{webisteName}}"><img style="margin-bottom: 0px;" src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Your password was successfully changed.</p><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">Use the following when prompted to log in: <br /> <strong>E-mail</strong>: {{email}} <br /> <strong>Password</strong>: {{password}}</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '6',
    'name' => 'Contact Us',
    'subject' => 'Contact Us request send by {{name}}',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a href="{{url}}"> <img style="margin-bottom: 0px;" src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Below are the details of Contact Request:</h1></td></tr><tr><td valign="top"><div style="background: #FFFFFF; margin: 10px 0 20px 0;"><fieldset style="border: 1px solid #EEEEEE;"><legend style="background: #eaf3f6; border: 1px solid #EEEEEE; color: #000000; font-size: 12px; font-weight: bold; padding: 0 8px;">Contact Information</legend><table style="width: 100%;" border="0" cellspacing="0" cellpadding="5"><tbody><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Name</td><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{name}}</td></tr><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Email</td><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{email}}</td></tr><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Subject</td><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{subject}}</td></tr><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">Message</td><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{message}}</td></tr></tbody></table></fieldset></div></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '7',
    'name' => 'Contact Us Reply',
    'subject' => 'Reply To: {{subject}}',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a href="{{url}}"> <img style="margin-bottom: 0px;" src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Hello {{name}},</h1></td></tr><tr><td valign="top"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="5"><tbody><tr><td style="font-size: 12px; line-height: 16px;" colspan="2"><strong>Thank you for contacting us.</strong></td></tr><tr><td style="font-size: 12px; line-height: 16px;" colspan="2">Below is the reply to your query:</td></tr><tr><td style="font-size: 12px; line-height: 16px;" colspan="2"><div style="background: #FFFFFF; margin: 10px 0 20px 0;"><fieldset style="border: 1px solid #EEEEEE;"><legend style="background: #eaf3f6; border: 1px solid #EEEEEE; color: #000000; font-size: 12px; font-weight: bold; padding: 0 8px;">Reply</legend><table style="width: 100%;" border="0" cellspacing="0" cellpadding="5"><tbody><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{reply}}</td></tr></tbody></table></fieldset></div><div style="background: #FFFFFF; margin: 20px 0;"><fieldset style="border: 1px solid #EEEEEE;"><legend style="background: #eaf3f6; border: 1px solid #EEEEEE; color: #000000; font-size: 12px; font-weight: bold; padding: 0 8px;">Query</legend><table style="width: 100%;" border="0" cellspacing="0" cellpadding="5"><tbody><tr><td style="font-size: 12px; line-height: 16px; margin: 0 0 16px 0;">{{message}}</td></tr></tbody></table></fieldset></div></td></tr></tbody></table></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><p style="font-size: 12px; margin: 0;">Kind Regards<br /> <strong>{{websiteName}}</strong></p></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '8',
    'name' => 'Admin Forgot Password',
    'subject' => 'Forgot Account Password',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{name}},</h1><p style="font-size: 12px; line-height: 16px; margin: 0 0 8px 0;">&nbsp;</p><p style="font-size: 12px; line-height: 16px; margin: 0;">To change your password <a href="{{link}}">Click Here</a></p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again<br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '9',
    'name' => 'Newsletter Subscription Confirmation',
    'subject' => 'Newsletter Subscription Confirmation',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Thank you for subscribing to our newsletters.</h1><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">To begin receiving the newsletters, you must first confirm your subscription by <a style="color: #1e7ec8;" href="{{link}}">Clicking Here</a></p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again, <br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '10',
    'name' => 'Newsletter Subscription Success',
    'subject' => 'Newsletter Subscription Success',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">You have successfully subscribed for our newsletters.</h1><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">If you do not want to recieve newsletters from our site, you can unsubscribe anytime by <a title="Unsubscribe" href="{{unsubscribe_link}}">clicking here</a>.</p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again, <br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));
$installer->insertRecord(Fox::getTableName('core_email_template'), array(
    'id' => '11',
    'name' => 'Newsletter Unsubscription Success',
    'subject' => 'Newsletter Unsubscription Success',
    'content' => '<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="padding: 20px 0 20px 0;" align="center" valign="top"><table style="border: 1px solid #e0e0e0; width: 650px;" border="0" cellspacing="0" cellpadding="7" bgcolor="FFFFFF"><tbody><tr><td valign="top"><a style="color: #1e7ec8;" href="{{url}}"><img src="{{themeUrl=images/logo_email.jpg}}" alt="{{websiteName}}" border="0" /></a></td></tr><tr><td valign="top"><h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Newsletter unsubscription was successful.</h1><p style="border: 1px solid #E0E0E0; font-size: 12px; line-height: 16px; margin: 0; padding: 13px 18px; background: #eaf3f6;">You will be no longer able to get newsletter from our website.</p><p>&nbsp;</p></td></tr><tr><td style="background: #dfedf1; text-align: center;" align="center"><center><p style="font-size: 12px; margin: 0;">Thank you again, <br /> <strong>{{websiteName}}</strong></p></center></td></tr></tbody></table></td></tr></tbody></table></div>',
    'created_date' => gmdate('Y-m-d H:i:s'),
    'modified_date' => gmdate('Y-m-d H:i:s'),
));

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_role_resource') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource` text NOT NULL,
  `resource_path` varchar(150) DEFAULT NULL,
  `action` text COMMENT '-1=>All Actions',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `module` varchar(255) NOT NULL,
  `display_in_tree` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE " . Fox::getTableName('admin_role_resource') . "
  ADD CONSTRAINT `admin_role_resource_ibfk_1` FOREIGN KEY (`module`) REFERENCES " . Fox::getTableName('core_module') . " (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->runSetup("
CREATE TABLE IF NOT EXISTS " . Fox::getTableName('admin_role_resource_category') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `resource_ids` text,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `module` varchar(255) NOT NULL,
  `access_all` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>No 1=>Yes',
  `parent_id_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE " . Fox::getTableName('admin_role_resource_category') . "
  ADD CONSTRAINT `admin_role_resource_category_ibfk_1` FOREIGN KEY (`module`) REFERENCES " . Fox::getTableName('core_module') . " (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
");
?>
