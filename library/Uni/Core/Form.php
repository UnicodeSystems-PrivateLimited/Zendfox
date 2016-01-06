<?php

/**
 * @see Zend_Form
 */
require_once 'Zend/Form.php';

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
 * Class Uni_Core_Form
 * 
 * @uses        Zend_Form
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Form extends Zend_Form {

    /**
     * Model object of related model
     * 
     * @var Uni_Core_Model
     */
    protected $model;

    /**
     * Constructor
     *
     * @param mixed $options
     * @return void
     */
    function __construct($options = null) {
        parent::__construct($options);
        $this->setPluginLoader(new Zend_Loader_PluginLoader(array(
                    'Uni_Core_Form_Element_' => 'Uni/Core/Form/Element',
                    'Zend_Form_Element_' => 'Zend/Form/Element'
                )), 'element');
    }

    /**
     * Gets object of related model
     * 
     * @return Uni_Core_Model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets objects of related model
     * 
     * @param Uni_Core_Model $model
     * @return void
     */
    public function setModel($model) {
        $this->model = $model;
        $eleKeys = array();
        $subForms = $this->getSubForms();
        foreach ($subForms as $subForm) {
            $eleKeys = array_merge($eleKeys, array_keys($subForm->getElements()));
        }        
        $data = $this->model->getData($eleKeys);        
        $this->setFormData($data);
    }
    
    /**
     * Sets data in the form to be populated
     * 
     * @param array $formData
     * @return void
     */
    public function setFormData($formData){
        isset($formData) && $this->populate($formData);
    }

}