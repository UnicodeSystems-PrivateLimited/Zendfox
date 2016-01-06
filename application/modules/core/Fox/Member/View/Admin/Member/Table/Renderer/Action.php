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
 * Class Fox_Member_View_Admin_Member_Table_Renderer_Action
 * 
 * @uses Fox_Core_View_Renderer_Abstract
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Admin_Member_Table_Renderer_Action extends Fox_Core_View_Renderer_Abstract {

    /**
     * Render contents
     * 
     * @return string
     */
    public function render() {
        $args = func_get_args();
        $changePwdImg = Fox::getThemeUrl('images/change_pwd.png');
        $loginImg = Fox::getThemeUrl('images/login.png');
        return '<a target="_self" href="' . $args[0]['change_password'] . '" title="' . $args[0]['change_password'] . '"><img src="' . $changePwdImg . '" alt="Change Password" title="Change Password" style="border:none;" /></a>&nbsp; <a target="_blank" href="' . $args[0]['login'] . '" title="' . $args[0]['login'] . '"><img src="' . $loginImg . '" alt="Login" title="Login" style="border:none;" /></a>';
    }

}