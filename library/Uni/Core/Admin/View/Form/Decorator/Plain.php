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
 * Class Uni_Core_Admin_View_Form_Decorator_Tabs
 * 
 * @uses        Zend_Form_Decorator_Abstract
 * @category    Uni
 * @package     Uni_Core
 * @subpackage  Uni_Core_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Admin_View_Form_Decorator_Tabs extends Zend_Form_Decorator_Abstract {

    protected $tabContent=array();

    public function getOptions()
    {
        if (null !== ($element = $this->getElement())) {
            if ($element instanceof Zend_Form) {
                $element->getAction();
                $method = $element->getMethod();
                if ($method == Zend_Form::METHOD_POST) {
                    $this->setOption('enctype', 'application/x-www-form-urlencoded');
                }
                foreach ($element->getAttribs() as $key => $value) {
                    $this->setOption($key, $value);
                }
            } elseif ($element instanceof Zend_Form_DisplayGroup) {
                foreach ($element->getAttribs() as $key => $value) {
                    $this->setOption($key, $value);
                }
            }
        }

        if (isset($this->_options['method'])) {
            $this->_options['method'] = strtolower($this->_options['method']);
        }

        return $this->_options;
    }

    protected function getLabelHTML($element) {
        $label = $element->getLabel();
        if (($translator = $element->getTranslator())) {
            $label = $translator->translate($label);
        }
        if ($element->isRequired()) {
            $label .= '*';
        }
        $label .= ':';
        return $element->getView()
                ->formLabel($element->getName(), $label);
    }

    protected function getInputHTML($element) {
        $helper = $element->helper;
        return $element->getView()->$helper(
                $element->getName(),
                $element->getValue(),
                $element->getAttribs(),
                $element->options
        );
    }

    protected function getErrorsHTML($element) {
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="errors">' .
        $element->getView()->formErrors($messages) . '</div>';
    }

    protected function getDescriptionHTML($element) {
        $desc = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="description">' . $desc . '</div>';
    }

    protected function getTabHTML(){
        $output='<ul class="tabs">';
        $tabCount=count($this->tabContent);
        for($i=0;$i<$tabCount;$i++){
            $output=$output.'<li class="tab'.(($i==0)?' first':(($i+1==$tabCount)?' last':'')).'" title="'.$this->tabContent[$i]['desc'].'"><span class="title">'.$this->tabContent[$i]['title'].'</span></li>';
        }
        return $output.'</ul>';
    }

    protected function getElementMarkup($element,$content) {        
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }
        if($element instanceof Zend_Form_Element_Hidden){
            return $this->getInputHTML($element);
        }
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->getLabelHTML($element);
        $input = $this->getInputHTML($element);
        $errors = $this->getErrorsHTML($element);
        $desc = $this->getDescriptionHTML($element);

        $output = '<tr class="row"><td class="label">'
                . $label
        .'</td><td class="ele">'
                . $input
                . $desc
                . $errors
                . '</td></tr>';

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }

    protected function getSubFormMarkup(Zend_Form_SubForm $form) {
        $content='<div id="'.$form->getId().'" class="tab">'.
                '<h2>'.$form->getLegend().'</h2>'.
                '<table cellspacing="0" cellpadding="0" border="0" align="left"><tbody>';
        $this->tabContent[]=array('title'=>$form->getLegend(),'desc'=>$form->getDescription());
        $output='';
        $elements=$form->getElements();
        if(is_array($elements)){
            foreach($elements as $element){
                $element->setView($form->getView());
                $output=$this->getElementMarkup($element,$output);
            }
        }
        return $content.$output.'</tbody></table></div>';
    }

    protected function getFormTag(array $options){
        $content='<form';
        foreach($options as $k=>$option){
            $content=$content.' '.$k.'="'.$option.'"';
        }
        return $content.'>';
    }

    public function render($content) {
        $form = $this->getElement();
        if (!$form instanceof Zend_Form) {
            throw new Exception('Element passed to ' . __METHOD__ . ' is not an instance of Zend_Form');
        }
        $attribs       = $this->getOptions();
        $name          = $form->getFullyQualifiedName();
        $attribs['id'] = $form->getId();
        $output='';
        foreach ($form->getSubForms() as $subForm) {
            $subForm->setView($form->getView());
            $output.=$this->getSubFormMarkup($subForm);
        }
        $content=$content.'<div class="tab_cont">'.
        $this->getTabHTML().
        '</div><div class="form_cont"><div class="tab_cont_bg_top">
<div class="tab_cont_bg_bottom">
<div class="tab_cont_bg_left">
<div class="tab_cont_bg_right">
<div class="tab_cont_bg_top_left">
<div class="tab_cont_bg_bottom_left">
<div class="tab_cont_bg_top_right">
<div class="tab_cont_bg_bottom_right">
<div class="tab_cont_bg">
<div class="tab_content_container">';
        $content=$content.$this->getFormTag($attribs).$output;
        return $content.'</form></div></div></div></div></div></div></div></div></div></div></div>';
    }

}