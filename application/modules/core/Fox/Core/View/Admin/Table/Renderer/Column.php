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
 * Class Fox_Core_View_Admin_Table_Renderer_Column
 * 
 * @uses Fox_Core_View_Renderer_Abstract
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Table_Renderer_Column extends Fox_Core_View_Renderer_Abstract {
    const TYPE_OPTIONS='options';
    const TYPE_DATE='date';

    /**
     * Render table column
     * 
     * @param Uni_Core_Model $item
     * @param array $column
     * @return string
     */
    public function renderTableColumn(Uni_Core_Model $item, array $column) {
        return $this->render($item, $column);
    }

    /**
     * Render contents
     * 
     * @return string
     * @throws Exception if arguments passed is less than two 
     */
    public function render() {
        if (func_num_args() < 2) {
            throw new Exception('Too few parameters! Two required.');
        }
        $args = func_get_args();
        $item = $args[0];
        $column = $args[1];
        $value = $item->{$column['fieldMethod']}();
        if ($column['type'] == self::TYPE_OPTIONS) {
            $colMarkup = $column['options'][$value];
        } else if ($column['type'] == self::TYPE_DATE) {
            $colMarkup = ($value != NULL && $value != '0000-00-00' && $value != '0000-00-00 00:00:00') ? Fox_Core_Model_Date::__toLocaleDate(strtotime($value), (isset($column['format']) ? $column['format'] : 'Y-m-d'), (isset($column['locale']) ? $column['locale'] : NULL)) : ((isset($column['default']) ? $column['default'] : ''));
        } else {
            $colMarkup = $value;
        }
        return $colMarkup;
    }

}