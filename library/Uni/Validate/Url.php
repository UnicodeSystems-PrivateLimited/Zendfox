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
 * Class Uni_Validate_Url
 * validates string pattern for url
 * 
 * @uses        Zend_Validate_Abstract
 * @category    Uni
 * @package     Uni_Validate 
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in> 
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Validate_Url extends Zend_Validate_Abstract {
    
    const INVALID_URL = 'invalidUrl';

    /**
     * Message templates
     * 
     * @var array 
     */
    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL.",
    );

    /**
     * Validates whether the given url is valid url or not
     *
     * @param string $value
     * @return boolean 
     */
    public function isValid($value) {
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        return true;
    }

}