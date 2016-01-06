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
 * Class Uni_Core_Admin_View_Form_Decorator_Abstract
 * 
 * @uses        Zend_Form_Decorator_Abstract
 * @category    Uni
 * @package     Uni_Core
 * @subpackage  Uni_Core_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Admin_View_Form_Decorator_Abstract extends Zend_Form_Decorator_Abstract {

    /**
     * @var string 
     */
    protected $belongsTo;

    /**
     *
     * @param string $baseBelongsTo
     * @param string $belongsTo
     * @return string 
     */
    public function mergeBelongsTo($baseBelongsTo, $belongsTo) {
        $endOfArrayName = strpos($belongsTo, '[');

        if ($endOfArrayName === false) {
            return $baseBelongsTo . '[' . $belongsTo . ']';
        }

        $arrayName = substr($belongsTo, 0, $endOfArrayName);

        return $baseBelongsTo . '[' . $arrayName . ']' . substr($belongsTo, $endOfArrayName);
    }

    /**
     *
     * @return array 
     */
    public function getOptions() {
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

    /**
     * Gets html content fot label
     *
     * @param Zend_Form_Element $element
     * @return string 
     */
    protected function getLabelHTML($element) {
        $label = $element->getLabel();
        if (($translator = $element->getTranslator())) {
            $label = $translator->translate($label);
        }
        $label=$element->getView()
                ->formLabel($element->getName(), $label);
        if ($element->isRequired()) {
            $label .= '<span class="asterisk">*</span>';
        }
        return $label;
    }

    /**
     * Gets form element html
     *
     * @param Zend_Form_Element $element
     * @return string 
     */
    protected function getInputHTML($element) {
        $helper = $element->helper;
        $elBelongsTo=$element->getBelongsTo();
        if (!empty($this->belongsTo)) {
            $name = $this->mergeBelongsTo($this->belongsTo, $element->getBelongsTo());
            $name.='[' . $element->getName() . ']';
        } else if(!empty($elBelongsTo)){
            $name = $this->mergeBelongsTo($elBelongsTo, $element->getName());
        }else{
            $name=$element->getName();
        }
        $value=$element->getValue();
        //echo 'Name:'.$name.' | Class:'.  get_class($element).' | Helper:'.$helper.'['.$value.']'.'<br/>';
        return $element->getView()->$helper(
                $name, $value, $element->getAttribs(), $element->options
        );
    }

    /**
     *
     * @param Zend_Form_Element $element
     * @return string 
     */
    protected function getErrorsHTML($element) {
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="errors">' .
        $element->getView()->formErrors($messages) . '</div>';
    }

    /**
     *
     * @param Zend_Form_Element $element
     * @return string 
     */
    protected function getDescriptionHTML($element) {
        $desc = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="description">' . $desc . '</div>';
    }

    /**
     *
     * @param Zend_Form_Element $element
     * @param string $content
     * @return string 
     */
    protected function getElementMarkup($element, $content) {
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }
        if ($element instanceof Zend_Form_Element_Hidden) {
            return $content.$this->getInputHTML($element);
        }
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->getLabelHTML($element);
        $input = $this->getInputHTML($element);
        $errors = $this->getErrorsHTML($element);
        $desc = $this->getDescriptionHTML($element);

        $output = '<tr class="row"><td class="label">'
                . $label
                . '</td><td class="ele">'
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
    
    /**
     *
     * @param Uni_Core_View $view
     * @param string $content
     * @return string 
     */
    protected function getViewMarkup($view, $content) {
        if (!$view instanceof Uni_Core_View || !$view) {
            return $content;
        }
        $output = '<tr class="row"><td colspan="2" class="ele view">'
                . $view->renderView()
                . '</td></tr>';
        return $content . $output;
    }

    /**
     * Composes form tag
     *
     * @param array $options
     * @return string 
     */
    protected function getFormTag(array $options) {
        $content = '<form';
        foreach ($options as $k => $option) {
            $content = $content . ' ' . $k . '="' . $option . '"';
        }
        return $content . '>';
    }    

}