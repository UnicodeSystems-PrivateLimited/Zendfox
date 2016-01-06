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
 * Class Fox_Eav_View_Admin_Set_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Set_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('eav/set');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $sets = array('' => '');
        $entityTypeModel = Fox::getModel('eav/entity/type');
        $collection = $entityTypeModel->getCollection();
        $entityTypes = array('' => '');
        foreach ($collection as $record) {
            $entityTypes[$record['id']] = ucfirst(Fox::getCamelCase($record['entity_type_code']));
        }
        $form->setName('set_page')
                ->setMethod('post');

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Attribute Set Information');
        $subForm1->setDescription('Attribute Set Information');

        $idField = new Zend_Form_Element_Hidden('id');
        $attributeSetField = new Zend_Form_Element_Text('attribute_set_name', array('size' => '30', 'maxlength' => 100, 'class' => 'required'));
        $attributeSetField->setRequired(true)
                ->setLabel('Attribute Set Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $entityTypeId = new Zend_Form_Element_Select('entity_type_id', array('class' => 'required', 'onchange' => 'getAttributeSets(this)'));
        $entityTypeId->setMultiOptions($entityTypes)
                ->setRequired(true)
                ->setLabel('Entity Type')
                ->addValidator('NotEmpty');

        $attributeSetBasedonField = new Zend_Form_Element_Select('attribute_set_based_on');
        $attributeSetBasedonField->setMultiOptions($sets)
                ->setRequired(false)
                ->setLabel('Based On')
                ->setDescription('Inherits the attributes and groups from the specified set')
                ->addValidator('NotEmpty');


        $subForm1->addElements(array($idField, $attributeSetField, $entityTypeId, $attributeSetBasedonField));
        $form->addSubForm($subForm1, 'subform1');

        parent::_prepareForm($form);
    }

}