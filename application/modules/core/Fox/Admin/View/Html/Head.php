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
 * Class Fox_Admin_View_Html_Head
 * 
 * @uses Fox_Core_View_Html_Head
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_Html_Head extends Fox_Core_View_Html_Head {

    /**
     * Page title separator
     * 
     * @var string
     */
    private $_titleSeparartor = ', ';

    /**
     * Get page title
     * 
     * @return string 
     */
    public function getTitle() {
        $helper = Fox::getHelper('core/head');
        $title = $helper->getTitle();
        return htmlspecialchars(html_entity_decode(implode($this->_titleSeparartor, $title)));
    }

}