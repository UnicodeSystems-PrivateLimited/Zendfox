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
 * Class Uni_Core_Form_SubForm
 * 
 * @uses        Uni_Core_Form
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Form_SubForm extends Uni_Core_Form {

    protected $_subViews = array();
    /**
     * Whether or not form elements are members of an array
     * @var boolean
     */
    protected $_isArray = true;

    public function addSubView(Uni_Core_Form_View $view) {
        $key = $view->getViewKey();        
        $this->_subViews[$key] = $view;        
        $view->setOrder(count($this->_elements));
    }

    public function getSubViews() {
        return $this->_subViews;
    }

    /**
     * Load the default decorators
     *
     * @return Zend_Form_SubForm
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                    ->addDecorator('HtmlTag', array('tag' => 'dl'))
                    ->addDecorator('Fieldset')
                    ->addDecorator('DtDdWrapper');
        }
        return $this;
    }

}
