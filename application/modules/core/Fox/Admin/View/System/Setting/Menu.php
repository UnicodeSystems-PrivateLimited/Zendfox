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
 * Class Fox_Admin_View_System_Setting_Menu
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Setting_Menu extends Fox_Core_View_Template {

    /**
     * Get menu html
     * 
     * @return string
     */
    function getMenuMarkup() {
        $markup = '';
        $settingModel = Fox::getModel('admin/system/setting/config');
        if ($settingModel) {
            $settingModel->loadSettingData($this->getRequest()->getParam('item', 'general'));
            $markup = $settingModel->getMenuMarkup();
        }
        return $markup;
    }

}
