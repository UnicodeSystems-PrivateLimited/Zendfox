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
 * Class Fox_Core_View_Html_Head
 * 
 * @uses Uni_Core_View
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Html_Head extends Uni_Core_View {

    /**
     * Page title separator
     * 
     * @var string
     */
    private $_titleSeparartor = ' ';

    /**
     * Get title separator
     * 
     * @return string 
     */
    public function getTitleSeparator() {
        return $this->_titleSeparartor;
    }

    /**
     * Set title separator
     * 
     * @param string $titleSeparartor 
     */
    public function setTitleSeparator($titleSeparartor) {
        $this->_titleSeparartor = $titleSeparartor;
    }

    /**
     * Get page title
     * 
     * @return string 
     */
    public function getTitle() {
        $titlePrefix = Fox::getPreference('web/head/title_prefix');
        $titleSuffix = Fox::getPreference('web/head/title_suffix');
        $helper = Fox::getHelper('core/head');
        $title = $helper->getTitle();
        if (empty($title)) {
            $helper->setTitle(Fox::getPreference('web/head/default_title'));
        }
        if ($titlePrefix) {
            $helper->addTitle($titlePrefix, FALSE);
        }
        if ($titleSuffix) {
            $helper->addTitle($titleSuffix, TRUE);
        }
        $title = $helper->getTitle();
        return htmlspecialchars(html_entity_decode(implode($this->_titleSeparartor, $title)));
    }

    /**
     * Get meta keywords
     * 
     * @return string
     */
    public function getMetaKeywords() {
        $metaKeywords = Fox::getHelper('core/head')->getMetaKeywords();
        if (!$metaKeywords) {
            $metaKeywords = Fox::getPreference('web/head/default_keywords');
        }
        return $metaKeywords;
    }

    /**
     * Get meta description
     * 
     * @return string
     */
    public function getMetaDescription() {
        $metaDescription = Fox::getHelper('core/head')->getMetaDescription();
        if (!$metaDescription) {
            $metaDescription = Fox::getPreference('web/head/default_description');
        }
        return $metaDescription;
    }

    /**
     * Get robots
     * 
     * @return string
     */
    public function getRobots() {
        return Fox::getPreference('web/head/default_robots');
    }

    /**
     * Get scripts
     * 
     * @return string
     */
    public function getScripts() {
        return Fox::getPreference('web/head/miscellaneous_scripts');
    }

    /**
     * Get favicon image path
     * 
     * @return string
     */
    public function getFavicon() {
        $imgName = Fox::getPreference('web/head/favicon_image');
        if($imgName && file_exists(Fox::getUploadDirectoryPath(). DIRECTORY_SEPARATOR . Fox_Core_Model_Preference::CORE_UPLOAD_FOLDER . DIRECTORY_SEPARATOR .$imgName)){
            return Fox::getUploadDirectoryUrl() . '/' . Fox_Core_Model_Preference::CORE_UPLOAD_FOLDER . '/' . $imgName;
        }else{
            return $this->themeUrl('images/default_favicon.ico');
        }
    }

}