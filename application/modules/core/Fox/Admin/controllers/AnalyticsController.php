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
 * Class Admin_AnalyticsController
 *
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_AnalyticsController extends Fox_Admin_Controller_Action {
    /**
     * Index action
     */
    function indexAction() {
        $filepath=CACHE_DIR.'/analytics/report-'.Fox_Core_Model_Date::getCurrentDate('Y-m-d').'.xml';
        $xDom = new Uni_Data_XDOMDocument();
        $xDom->load($filepath);
        header('Content-Type: text/xml');
        echo $xDom->saveXML();
    }

}
