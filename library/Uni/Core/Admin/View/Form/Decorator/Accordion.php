<?php

/**
 * @see Uni_Core_View_Helper_FormEditor
 */
include_once 'Uni/Core/View/Helper/FormEditor.php';

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
 * Class Uni_Core_Admin_View_Form_Decorator_Accordion
 * generate form contents to create accordion for forms
 * 
 * @uses        Uni_Core_Admin_View_Form_Decorator_Abstract
 * @category    Uni
 * @package     Uni_Core
 * @subpackage  Uni_Core_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Admin_View_Form_Decorator_Accordion extends Uni_Core_Admin_View_Form_Decorator_Abstract {

    /**
     *
     * @param Zend_Form_SubForm $form
     * @return string 
     */
    protected function getSubFormMarkup(Zend_Form_SubForm $form) {
        $content = '<h3 class="accordion-header"><a href="#">' . $form->getLegend() . '</a></h3>' .
                '<div id="' . $form->getId() . '" class="accordion-content"><ul><li>' .
                '<table cellspacing="0" cellpadding="0" border="0" align="left"><tbody>';
        $output = '';
        $elements = $form->getElements();
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $element->setView($form->getView());
                $output = $this->getElementMarkup($element, $output);
            }
        }
        return $content . $output . '</tbody></table></li></ul></div>';
    }
    
    /**
     *
     * @param string $content
     * @return string 
     */
    public function render($content) {
        $form = $this->getElement();
        if (!$form instanceof Zend_Form) {
            throw new Exception('Element passed to ' . __METHOD__ . ' is not an instance of Zend_Form');
        }
        $this->belongsTo = $form->getElementsBelongTo();
        $attribs = $this->getOptions();
        $name = $form->getFullyQualifiedName();
        $attribs['id'] = $form->getId();
        $output = '';
        $elements = $form->getElements();
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $element->setView($form->getView());
                $output = $this->getElementMarkup($element, $output);
            }
        }
        foreach ($form->getSubForms() as $subForm) {
            $subForm->setView($form->getView());
            $output.=$this->getSubFormMarkup($subForm);
        }
        $content = $content . '<div class="form_cont"><div class="tab_cont_bg_top">
<div class="tab_cont_bg_bottom">
<div class="tab_cont_bg_left">
<div class="tab_cont_bg_right">
<div class="tab_cont_bg_top_left">
<div class="tab_cont_bg_bottom_left">
<div class="tab_cont_bg_top_right">
<div class="tab_cont_bg_bottom_right">
<div class="tab_cont_bg">
<div class="tab_content_container">';
        $content = $content . $this->getFormTag($attribs) . '<div class="accordion">' . $output . '</div>';
        return $content . '</form></div></div></div></div></div></div></div></div></div></div></div>';
    }

}