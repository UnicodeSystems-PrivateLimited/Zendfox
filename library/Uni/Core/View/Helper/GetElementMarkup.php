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
 * Class Uni_View_Helper_GetElementMarkup
 * Helper class to generate form element markup
 * 
 * @uses        Zend_View_Helper_Abstract
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_View_Helper_GetElementMarkup extends Zend_View_Helper_Abstract {

    /**
     * Method for helper calling
     *
     * @param array $elementData
     * @return string 
     */
    function getElementMarkup(array $elementData=array()) {
        $html='';
        $helper='form'.ucfirst($elementData['type']);
        $default=(isset($elementData['default'])?$elementData['default']:NULL);
        $options=(isset($elementData['options'])?$elementData['options']:array());
        switch ($elementData['type']) {
            case 'select':
            case 'checkbox':
            case 'multicheckbox':
            case 'radio':
                $multiOptions=(isset($elementData['multiOptions'])?$elementData['multiOptions']:array());
                $html=$this->view->$helper($elementData['name'],$default,$options,$multiOptions);
                break;
            default:
                $html=$this->view->$helper($elementData['name'],$default,$options);
                break;
        }
        return $html;
    }

}