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
 * Class Fox_Core_View_Admin_Form
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Form extends Uni_Core_Admin_View {

    /**
     * @var string
     */
    protected $id;
    /**
     * Form object
     * 
     * @var object
     */
    protected $form;
    /**
     * Model key
     * 
     * @var string
     */
    protected $modelKey;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('fox/containers/form');
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
     * Set model key
     * 
     * @param string $key 
     */
    protected function setModelKey($key) {
        $this->modelKey = $key;
    }

    /**
     * Get model key
     * 
     * @return string
     */
    public function getModelKey() {
        return $this->modelKey;
    }

    /**
     * Set form object
     * 
     * @param object $form 
     */
    public function setForm($form) {
        $this->form = $form;
        $this->form->setView($this);
    }

    /**
     * Get form object
     * 
     * @return object
     */
    public function getForm() {
        if (NULL == $this->form) {
            $this->setForm(new Uni_Core_Form());
        }
        return $this->form;
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        if (NULL == $this->id) {
            $this->setId(get_class($this));
        }
        $form->setAttrib('id', $this->id);
        $model = Fox::getModel($this->modelKey);
        if (($formData = Fox::getModel('core/session')->getFormData())) {
            Fox::getModel('core/session')->setFormData(NULL);
            $form->setFormData($formData);
        } else if ($model && $model->getId()) {
            $form->setModel($model);
        }
    }

    /**
     * Render contents
     * 
     * @return string
     */
    public function renderView() {
        $this->_prepareForm($this->getForm());
        return parent::renderView();
    }

}
