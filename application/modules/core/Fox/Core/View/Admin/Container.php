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
 * Class Fox_Core_View_Admin_Container
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Container extends Uni_Core_Admin_View {
    const FORM_KEY='__form__';
    /**
     * @var string
     */
    protected $id;
    /**
     * Header text
     * 
     * @var string
     */
    protected $headerText;
    /**
     * Form java scripts
     * 
     * @var array
     */
    protected $formScripts = array();
    /**
     * Default css class name for buttons
     * 
     * @var string
     */
    protected $defaultStyleClass = 'form-button1';
    /**
     * Default button icon css class
     * 
     * @var string
     */
    protected $defaultIconClass = 'plus';
    /**
     * Custom buttons
     * 
     * @var array
     */
    protected $customButtons = array();

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('fox/containers/container');
    }

    /**
     * Get id
     * 
     * @return string
     */
    public function getId() {
        if (NULL == $this->id) {
            $this->id = get_class($this);
        }
        return $this->id;
    }

    /**
     * Set id
     * 
     * @param string $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get header text
     * 
     * @return string
     */
    public function getHeaderText() {
        return $this->headerText;
    }

    /**
     * Set header text
     * 
     * @param string $headerText 
     */
    public function setHeaderText($headerText) {
        Fox::getHelper('core/head')->addTitle($headerText, FALSE);
        $this->headerText = $headerText;
    }

    /**
     * Get default css class name
     * 
     * @return string
     */
    public function getDefaultStyleClass() {
        return $this->defaultStyleClass;
    }

    /**
     * Set default css class name
     * 
     * @param string $defaultStyleClass 
     */
    public function setDefaultStyleClass($defaultStyleClass) {
        $this->defaultStyleClass = $defaultStyleClass;
    }

    /**
     * Get default css icon class name
     * 
     * @return string
     */
    public function getDefaultIconClass() {
        return $this->defaultIconClass;
    }

    /**
     * Set default css icon class name
     * 
     * @param string $defaultIconClass 
     */
    public function setDefaultIconClass($defaultIconClass) {
        $this->defaultIconClass = $defaultIconClass;
    }

    /**
     * Get custom buttons
     * 
     * @return array
     */
    public function getButtons() {
        return $this->customButtons;
    }

    /**
     * Add custom button
     * 
     * @param array $button 
     */
    public function addButton(array $button) {
        $this->customButtons[] = $button;
    }

    /**
     * Get form object
     * 
     * @return object
     */
    public function getForm() {
        if (($form = $this->getChild(self::FORM_KEY))) {
            return $form;
        } else {
            $formClass = get_class($this) . '_Form';
            $formView = Fox::getView($formClass);
            if ($formView) {
                $this->addChild(self::FORM_KEY, $formView);
                return $formView;
            }
        }
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        if (NULL == $this->id) {
            $this->id = get_class($this);
        }
        $formView = $this->getForm();
    }

    /**
     * Render contents
     * 
     * @return string
     */
    public function renderView() {
        $this->_prepareContainer();
        return parent::renderView();
    }

}