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
 * Class Fox_Extensionmanager_Model_Package_State
 * 
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Package_State {
    const RELEASE_ALPHA = 1;
    const RELEASE_BETA = 2;
    const RELEASE_STABLE = 3;

    /**
     * Public constructor
     */
    public function __construct() {
        
    }

    /**
     * Retrieve package stability options
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray() {
        return array(
            ''=>'',
            self::RELEASE_ALPHA => 'Alpha',
            self::RELEASE_BETA => 'Beta',
            self::RELEASE_STABLE => 'Stable'
        );
    }
    
    /**
     * Retrieve package stability text for corresponding value
     * 
     * @param int $value
     * @return string
     */    
    public function getOptionText($value){
        $data = $this->_getOptionArray();
        return isset($data[$value]) ? $data[$value] : '';
    }

}