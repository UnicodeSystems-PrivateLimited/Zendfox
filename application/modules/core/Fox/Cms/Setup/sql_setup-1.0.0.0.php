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
 * Class Fox_Cms_Setup_Sql
 * 
 * @uses Uni_Core_Setup_Abstract
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
$installer = $this;
$installer->runSetup("
    CREATE TABLE IF NOT EXISTS " . Fox::getTableName('cms_block') . " (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `identifier_key` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=>Disabled 1=>Enabled',
  `created_on` datetime NOT NULL,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier_key` (`identifier_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
");

$installer->insertRecord(
        Fox::getTableName('cms_block'), array(
    'id' => '1',
    'title' => 'Footer Content',
    'identifier_key' => 'footer-content',
    'content' => 'Powered By: Zendfox (WAF)',
    'status' => '1',
    'created_on' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_block'), array(
    'id' => '2',
    'title' => 'Footer Links',
    'identifier_key' => 'footer-links',
    'content' => '<p><a class="noneBack" href="{{getUrl(\'\')}}">Home</a> <a href="{{getUrl(\'about\')}}">About Us</a><a href="{{getUrl(\'need-help\')}}">Need Help? </a> <a href="{{getUrl(\'contact\')}}">Contact Us</a><a href="{{getUrl(\'privacy-policy\')}}">Privacy Policy</a><a class="last" href="{{getUrl(\'terms\')}}">Terms of Service</a></p>',
    'status' => '1',
    'created_on' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_block'), array(
    'id' => '3',
    'title' => 'Left Template',
    'identifier_key' => 'template-block',
    'content' => '<div class="left_cont top1 gradient"><div class="zend-template-content"><ul><li class="template-image">&nbsp;</li><li class="get-text">Get Zendfox<br /> Customizable Templates</li><li class="click-button"><a href="#">Click Here</a></li></ul></div></div>',
    'status' => '1',
    'created_on' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_block'), array(
    'id' => '4',
    'title' => 'Left Menu',
    'identifier_key' => 'left-menu-block',
    'content' => '<div class="left_cont top2 gradient">    <h2 class="sub-heading">Left Menu Title</h2>    <div class="left-list">        <ul>            <li><a href="#">Demo Menu 1</a></li>            <li><a href="#">Demo Menu 2</a></li>            <li><a href="#">Demo Menu 3</a></li>            <li><a href="#">Demo Menu 4</a></li>            <li><a href="#">Demo Menu 5</a></li>            <li><a href="#">Demo Menu 6</a></li>            <li><a href="#">Demo Menu 7</a></li>            <li><a href="#">Demo Menu 8</a></li>            <li><a href="#">Demo Menu 9</a></li>            <li><a href="#">Demo Menu 10</a></li>            <li><a href="#">Demo Menu 11</a></li>                    </ul>    </div></div>',
    'status' => '1',
    'created_on' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_block'), array(
    'id' => '5',
    'title' => 'Right Menu',
    'identifier_key' => 'right-menu-block',
    'content' => '<div class="left_cont top2 gradient">    <h2 class="sub-heading">Right Menu Title</h2>    <div class="left-list">        <ul>            <li><a href="#">Demo Menu 1</a></li>            <li><a href="#">Demo Menu 2</a></li>            <li><a href="#">Demo Menu 3</a></li>            <li><a href="#">Demo Menu 4</a></li>            <li><a href="#">Demo Menu 5</a></li>            <li><a href="#">Demo Menu 6</a></li>            <li><a href="#">Demo Menu 7</a></li>            <li><a href="#">Demo Menu 8</a></li>            <li><a href="#">Demo Menu 9</a></li>            <li><a href="#">Demo Menu 10</a></li>            <li><a href="#">Demo Menu 11</a></li>                    </ul>    </div></div>',
    'status' => '1',
    'created_on' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->runSetup("
    CREATE TABLE IF NOT EXISTS " . Fox::getTableName('cms_page') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `url_key` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `layout` varchar(100) NOT NULL,
  `layout_update` text NULL,
  `meta_keywords` tinytext NULL,
  `meta_description` tinytext NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=>Inactive,1=>Active,100=>Deleted',
  `created` datetime NOT NULL COMMENT 'Creation Date and time',
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_key` (`url_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
");
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '1',
    'title' => '404 Not Found!',
    'url_key' => 'no-page-404',
    'content' => '<div class="oops"><h1>Oooops!! <span class="go-back"><a onclick="history.go(-1);" href="#">Go back</a>&nbsp; to the previous page</span></h1><div class="not-found"><div class="logo-img"><img src="{{themeUrl(\'images/fox-logo.png\')}}" alt="Zendfox Logo" /></div><h2>404-Page Not Found!</h2><h3>Sorry, <span class="page-request">The page you requested was not found on server.</span></h3><h4>Suggestions:</h4><ul><li>Please make sure the spelling is correct.</li></ul></div></div>',
    'layout' => 'one-column',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '2',
    'title' => 'Home',
    'url_key' => 'home',
    'content' => '<div class="banner_wrap">{{view key="home-banner" class="core/template" tpl="fox/html/banner"}}</div><div class="msgbox">{{view key="messages" class="core/template" tpl="fox/html/messages"}}</div><div class="title_bar"><h2>Home <span>Page</span></h2></div><div class="home_content_wrap"><div class="home_content_left"><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p></div></div>',
    'layout' => 'three-columns',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '3',
    'title' => 'About Us',
    'url_key' => 'about',
    'content' => '<div class="title_bar"><h2>About <span>Us</span></h2></div><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p>',
    'layout' => 'three-columns',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '4',
    'title' => 'Privacy Policy',
    'url_key' => 'privacy-policy',
    'content' => '<div class="title_bar"><h2>Privacy <span>Policy</span></h2></div><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p>',
    'layout' => 'three-columns',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '5',
    'title' => 'Terms of Service',
    'url_key' => 'terms',
    'content' => '<div class="title_bar"><h2>Terms of <span>Service</span></h2></div><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p>',
    'layout' => 'three-columns',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
$installer->insertRecord(
        Fox::getTableName('cms_page'), array(
    'id' => '6',
    'title' => 'Need Help?',
    'url_key' => 'need-help',
    'content' => '<div class="title_bar"><h2>Need <span>Help?</span></h2></div><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p>',
    'layout' => 'three-columns',
    'layout_update' => '',
    'meta_keywords' => '',
    'meta_description' => '',
    'status' => '1',
    'created' => date('Y-m-d H:i:s'),
    'modified_on' => date('Y-m-d H:i:s'),
        )
);
?>
