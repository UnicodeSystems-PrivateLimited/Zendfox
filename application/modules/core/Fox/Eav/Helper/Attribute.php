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
 * Class Fox_Eav_Helper_Attribute
 * 
 * @uses Uni_Core_Helper
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_Helper_Attribute extends Uni_Core_Helper {

    /**
     * Get data type based on input type
     * 
     * @param string $inputType
     * @return string 
     */
    public function getDataType($inputType) {
        $dataType = '';
        switch ($inputType) {
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_TEXTFIELD:
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_SELECT:
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_MULTISELECT:
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_RADIO:
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_FILE:
                $dataType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_VARCHAR;
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_PRICE:
                $dataType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_DECIMAL;
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_TEXTAREA:
                $dataType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_TEXT;
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_BOOLEAN:
                $dataType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_INT;
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_DATE:
                $dataType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_DATE;
                break;
            default:
                $datType = Fox_Eav_Model_Attribute::ATTRIBUTE_DATA_TYPE_VARCHAR;
                break;
        }
        return $dataType;
    }

}