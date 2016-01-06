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
 * Class Uni_View_Helper_FormBoolean
 * generates form fields for boolean input
 * 
 * @uses        Zend_View_Helper_FormElement
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_View_Helper_FormBoolean extends Zend_View_Helper_FormElement {

    /**
     *
     * @var string 
     */
    public $no = '0';
    
    /**
     *
     * @var string 
     */
    public $yes = '1';

    /**
     * Method for helper calling
     *
     * @param string $name
     * @param string $value
     * @param array $attribs
     * @return string 
     */
    public function formBoolean($name, $value = null, $attribs = null) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['yesNoOption'])) {
            $attribs['yesNoOption'] = array($this->yes => 'Yes', $this->no => 'No');
        }

        // build the element
        $xhtml = '<select name="' . $this->view->escape($name) . '" id="' . $this->view->escape($id) . '" ' . $this->_htmlAttribs($attribs) . $disabled . '>'
                . '<option value="0" ' . ((!$this->view->escape($value)) ? ' selected="selected" ' : '') . ' />' . $attribs['yesNoOption'][$this->no] . '</option>'
                . '<option value="1" ' . (($this->view->escape($value)==1) ? ' selected="selected" ' : '') . ' />' . $attribs['yesNoOption'][$this->yes] . '</option>';
        return $xhtml;
    }

}