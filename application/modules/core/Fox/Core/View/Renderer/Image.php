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
 * Class Fox_Core_View_Renderer_Image
 * 
 * @uses Uni_Core_Renderer_Interface
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Renderer_Image implements Uni_Core_Renderer_Interface {

    /**
     * Render contents
     * 
     * @return string
     */
    public function render() {
        $args = func_get_args();
        return '<img src="' . $args[0] . '" alt="' . ((isset($args[1]) ? $args[1] : $args[0])) . '" ' . $this->getAttributeMarkup($args[2]) . '/>';
    }

    /**
     * Get attribute markup
     * 
     * @param array $options
     * @return string 
     */
    protected function getAttributeMarkup(array $options=array()) {
        $attrStr = '';
        $space = '';
        foreach ($options as $k => $v) {
            $attrStr = $attrStr . $space . $k . '="' . $v . '"';
            $space = ' ';
        }
        return $attrStr;
    }

}