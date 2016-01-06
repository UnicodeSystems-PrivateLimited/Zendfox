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
 * Class Fox_Navigation_View_Admin_Menugroup_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_View_Admin_Menugroup_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('navigation/menugroup');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('menugroup')
                ->setMethod('post');
        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Menu Group Information');
        $subForm1->setDescription('Menu Group Information');
        $idField = new Zend_Form_Element_Hidden('id');
        $name = new Zend_Form_Element_Text('name', array('class' => 'required', 'maxlength' => 200));
        $name->setRequired(true)
                ->setLabel('Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $status = new Zend_Form_Element_Select('status', array('class' => 'required', 'maxlength' => 200));
        $status->setRequired(true)
                ->setLabel('Status')
                ->setMultiOptions(Fox::getModel('navigation/menugroup')->getAllStatuses());

        $sort_order = new Zend_Form_Element_Text('sort_order', array('class' => 'required', 'maxlength' => 200));
        $sort_order->setRequired(true)
                ->setLabel('Sort Order')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $key = new Zend_Form_Element_Text('key', array('class' => 'required', 'maxlength' => 200));
        $key->setRequired(true)
                ->setLabel('Key')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $description = new Zend_Form_Element_Textarea('description', array('cols' => '60', 'rows' => '10'));

        $subForm1->addElements(array($idField, $name, $sort_order, $key, $status, $description));
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}