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
 * Class Fox_Eav_View_Admin_Attribute_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Attribute_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('eav/attribute/form');
        $this->setModelKey('eav/attribute');
        $this->setId('eav_attribute_add');
    }

    /**
     * Get attribute model object
     * 
     * @return Fox_Eav_Model_Attribute
     */
    public function getAttribute() {
        $id = $this->getRequest()->getParam('id');
        $model = Fox::getModel('eav/attribute');
        if ($id) {
            $model->load($id);
        }
        return $model;
    }

    /**
     * Get attribute option collection
     * 
     * @return Uni_Core_Model_Collection|NULL
     */
    public function getAttributeOptions() {
        $id = $this->getRequest()->getParam('id');
        $model = Fox::getModel('eav/attribute/option');
        $collection = NULL;
        if ($id) {
            $collection = $model->getCollection("attribute_id='$id'");
        }
        return $collection;
    }

    /**
     * Get entity type collection
     * 
     * @return Uni_Core_Model_Collection
     */
    public function getEntityTypes() {
        $entityTypeModel = Fox::getModel('eav/entity/type');
        return $entityTypeModel->getCollection();
    }

    /**
     * Get save button url
     * 
     * @return string
     */
    public function getSubmitUrl() {
        return Fox::getUrl('*/*/save');
    }

}