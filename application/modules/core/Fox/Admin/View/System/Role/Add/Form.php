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
 * Class Fox_Admin_View_System_Role_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Role_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('admin/role');
        $this->setId('admin_role');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('admin_Role')
                ->setMethod('post');

        $subForm1 = new Uni_Core_Form_SubForm();
        $subForm1->setLegend('Role Information');
        $subForm1->setDescription('General Role Information');
        $subForm2 = new Uni_Core_Form_SubForm();
        $subForm2->setLegend('Role Resources');
        $subForm2->setDescription('Role Resources');

        $idField = new Zend_Form_Element_Hidden('id');
        $resourceIds = new Zend_Form_Element_Hidden('resource_ids');
        $name = new Zend_Form_Element_Text('name', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $name->setRequired(true)
                ->setLabel('Role Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $access = new Zend_Form_Element_Select('resource_access', array('class' => 'required'));
        $access->setRequired(true)
                ->setLabel('Resource Access')
                ->setMultiOptions(Fox::getModel('admin/role')->getRoleAccess());

        $subForm1->addElements(array($idField, $name, $resourceIds));
        $treeView = Fox::getView('admin/system/role/add/tree');
        $subForm2->addElements(array($access));
        $subForm2->addSubView($treeView);
        $form->addSubForm($subForm1, 'subform1');
        $form->addSubForm($subForm2, 'subform2');

        parent::_prepareForm($form);
    }

}