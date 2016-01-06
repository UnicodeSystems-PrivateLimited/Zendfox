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
 * Class Fox_News_View_News
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_News
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_News_View_News extends Fox_Core_View_Template {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Get news list url
     * 
     * @return string
     */
    function getListUrl() {
        return Fox::getUrl('news');
    }

    /**
     * Get news collection
     * 
     * @return Uni_Core_Model_Collection
     */
    function getNews() {
        $currentDate = Fox_Core_Model_Date::getCurrentDate('Y-m-d');
        return Fox::getModel('news/news')->getCollection('status=' . Fox_News_Model_News::STATUS_ENABLED . " AND (('" . $currentDate . "' BETWEEN date_from AND date_to) OR (date_to>='" . $currentDate . "' AND date_from='0000-00-00') OR (date_from<='" . $currentDate . "' AND date_to='0000-00-00') OR (ISNULL(date_to) AND ISNULL(date_from)) OR (date_to='0000-00-00' AND date_from='0000-00-00'))",'*','sort_order');
    }

}