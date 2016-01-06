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
 * Class Uni_View_Helper_FormEditor
 * generates form fields for html editor
 * 
 * @uses        Zend_View_Helper_FormElement
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
  */
class Uni_View_Helper_FormEditor extends Zend_View_Helper_FormElement {

    /**
     *
     * @var int 
     */
    public $rows = 24;
    
    /**
     *
     * @var int
     */
    public $cols = 80;

    /**
     * Method for helper calling
     *
     * @param string $name
     * @param string $value
     * @param array $attribs
     * @return string 
     */
    public function formEditor($name, $value = null, $attribs = null) {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }
        $attribs['class'] = empty($attribs['class']) ? 'tinymce' : $attribs['class'] . ' tinymce';

        // build the element
        $eleId = $this->view->escape($id);
        $eleml = '<div class="show_editor">
            <a href="javascript:;" class="show_edit" onmousedown="$(\'#' . $eleId . '\').foxEditor(\'show\')">Show Editor</a>'
                . '<a href="javascript:;" class="show_edit" onmousedown="$(\'#' . $eleId . '\').foxEditor(\'hide\');">Hide Editor</a><br /></div>'
                . '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $eleId . '"'
                . $disabled
                . $this->_htmlAttribs($attribs) . '>'
                . $this->view->escape($value) . '</textarea>';
        $xhtml = <<<MARKUP
        $eleml
            <script type="text/javascript">
               <!--//--><![CDATA[//><!--               
               if(typeof $.fn.foxEditor != 'function'){                 
                 $('head').append('<script type="text/javascript" src="{$this->view->baseUrl('theme/global/js/foxEditor.js')}"></script>');
               }
               $('#$eleId').foxEditor({script_base:'{$this->view->baseUrl('theme/global/js/tinymce')}'});
              //--><!]]>
            </script>
MARKUP;
        return $xhtml;
    }

}