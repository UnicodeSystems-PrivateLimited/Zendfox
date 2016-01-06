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
 * Class Uni_View_Helper_FormDate
 * generates form fields for date input
 * 
 * @uses        Zend_View_Helper_FormElement
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_View_Helper_FormDate extends Zend_View_Helper_FormElement {

    /**
     * Method for helper calling
     *
     * @param string $name
     * @param string $value
     * @param array $attribs
     * @return string 
     */
    public function formDate($name, $value = null, $attribs = null) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }
        $options=(isset($attribs['options'])?$attribs['options']:array());
        if(!isset($options['changeMonth'])){
            $options['changeMonth']=true;
        }
        if(!isset($options['changeYear'])){
            $options['changeYear']=true;
        }
        if(!isset($options['showButtonPanel'])){
            $options['showButtonPanel']=true;
        }
        if(!isset($options['showOn'])){
            $options['showOn']='button';
        }
        if(!isset($options['buttonImage'])){
            $options['buttonImage']=Fox::getThemeUrl('images/calender.gif');
        }
        if(!isset($options['buttonImageOnly'])){
            $options['buttonImageOnly']=true;
        }
        if(!isset($options['dateFormat'])){
            $options['dateFormat']='yy-mm-dd';
        }
        
        $pickerOptions=Zend_Json_Encoder::encode($options);
        // build the element
        $eleml = '<input type="text" name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . ' value="' . $this->view->escape($value) . '" />';
        $xhtml = <<<MARKUP
<script type="text/javascript">
$(function() {
		$( "#{$this->view->escape($id)}" ).datepicker($pickerOptions);
});
</script>
$eleml    
MARKUP;
        return $xhtml;
    }

}