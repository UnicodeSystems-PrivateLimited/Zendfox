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
 * Class Fox_Navigation_View_Menu
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_View_Menu extends Fox_Core_View_Template {
    const ROOT_TAG='ul';
    const LEAF_TAG='li';
    const CACHE_MENU_FOLDER='menu';

    /**
     * Get menu html for group key LEFT
     * 
     * @return string
     */
    public function getLeftMenuHtml() {
        return $this->getMenu('LEFT');
    }

    /**
     * Get menu html for group key HEADER
     * 
     * @return string
     */
    public function getHeaderMenuHtml() {
        return $this->getMenu('HEADER');
    }

    /**
     * Get menu html
     * 
     * @param string $key Menu group key
     * @return string 
     */
    public function getMenu($key) {
        $menuHTML = '';
        $cacheSettings = Uni_Core_CacheManager::loadCacheSettings();
        $isCacheEnabled = (isset($cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_LAYOUT]) ? $cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_LAYOUT] : FALSE);
        $package = Fox::getPreference(Uni_Controller_Action::MODE_ADMIN . '/package');
        $theme = Fox::getPreference(Uni_Controller_Action::MODE_ADMIN . '/theme');
        $cacheMenuBasePath = CACHE_DIR . DIRECTORY_SEPARATOR . Fox_Core_Model_Cache::CACHE_CODE_LAYOUT . DIRECTORY_SEPARATOR . Uni_Controller_Action::MODE_WEB . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . self::CACHE_MENU_FOLDER;
        (file_exists($cacheMenuBasePath) && is_dir($cacheMenuBasePath)) || mkdir($cacheMenuBasePath, 0777, TRUE);
        $cacheMenuPath = $cacheMenuBasePath . DIRECTORY_SEPARATOR . $key . '.menu';
        if ($isCacheEnabled && file_exists($cacheMenuPath)) {
            $cacheMenuDoc = new DOMDocument();
            $cacheMenuDoc->loadHTMLFile($cacheMenuPath);
            $menuHTML = $cacheMenuDoc->saveXML();
            unset($cacheMenuDoc);
        } else {
            $menuGroupModel = Fox::getModel('navigation/menugroup');
            $menuGroupModel->load($key,'key');
            if ($menuGroupModel->getId() && $menuGroupModel->getStatus() == Fox_Navigation_Model_Menugroup::STATUS_ENABLED) {
                $menuModel = Fox::getModel('navigation/menu');
                $menuList = $menuModel->getMenuItemsByGroup($key);
                $mnuDoc = new DOMDocument();
                $mnuDoc->formatOutput = true;
                $mnuRoot = NULL;
                if (count($menuList)) {
                    $mnuRoot = Uni_Data_XDOMDocument::createNode(self::ROOT_TAG, array('class' => 'menu_container'), $mnuDoc);
                    $mnuDoc->appendChild($mnuRoot);
                    foreach ($menuList as $menu) {
                        $node = Uni_Data_XDOMDocument::createNode(self::LEAF_TAG, array('class' => 'menu_nav' . (($menu['style_class']) ? ' ' . $menu['style_class'] : '')), $mnuDoc);
                        if((strpos($menu['link'], 'http://', 0) === 0) || (strpos($menu['link'], 'https://', 0) === 0)){
                            $link=$menu['link'];
                        }else{
                            $link=Fox::getUrl($menu['link']);
                        }
                        $link = Uni_Data_XDOMDocument::createNode('a', array('href' => $link, 'target' => $menu['open_window'], 'class' => $menu['style_class']), $mnuDoc);
                        $link->appendChild($mnuDoc->createTextNode($menu['title']));
                        $node->appendChild($link);
                        $mnuRoot->appendChild($node);
                    }
                }
                if (isset($mnuDoc)) {
                    $menuHTML = $mnuDoc->saveXML($mnuRoot);
                    if ($isCacheEnabled) {
                        $mnuDoc->saveHTMLFile($cacheMenuPath);
                        @chmod($cacheMenuPath, 0777);
                    }
                }
                unset($mnuDoc);
            }
        }
        return $menuHTML;
    }

}