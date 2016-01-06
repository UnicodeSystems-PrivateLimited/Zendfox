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
 * Class Fox_Admin_View_System_User_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_User_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('admin/user');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $userId = $this->getRequest()->getParam('id', FALSE);
        $form->setName('admin_user')
                ->setMethod('post');

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('User Information');
        $subForm1->setDescription('General User Information');
        $subForm2 = new Zend_Form_SubForm();
        $subForm2->setLegend('User Role');
        $subForm2->setDescription('User Role');

        $idField = new Zend_Form_Element_Hidden('id');
        $username = new Zend_Form_Element_Text('username', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $username->setRequired(true)
                ->setLabel('Username')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDescription('Username should be unique')
                ->addValidator('NotEmpty');

        $firstname = new Zend_Form_Element_Text('firstname', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $firstname->setRequired(true)
                ->setLabel('First Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $lastname = new Zend_Form_Element_Text('lastname', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $lastname->setRequired(true)
                ->setLabel('Last Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $password = new Zend_Form_Element_Password('password', array('size' => '30', 'maxlength' => 200, 'class' => (!$userId ? 'required' : '')));
        if (!$userId) {
            $password->setRequired(true);
        }
        $password->setLabel('Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $cPassword = new Zend_Form_Element_Password('c_password', array('size' => '30', 'maxlength' => 200, 'class' => (!$userId ? 'required' : '')));
        if (!$userId) {
            $cPassword->setRequired(true);
        }
        $cPassword->setLabel('Confirm Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $email = new Zend_Form_Element_Text('email', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $email->setRequired(true)
                ->setLabel('Email ID')
                ->addFilter('StripTags')
                ->setDescription('Email ID should be unique')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $contact = new Zend_Form_Element_Text('contact', array('size' => '30', 'maxlength' => 200));
        $contact->setLabel('Contact')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $status = new Zend_Form_Element_Select('status', array('class' => 'required'));
        $status->setRequired(true)
                ->setLabel('Status')
                ->setMultiOptions(Fox::getModel('admin/user')->getAllStatuses());

        $roleId = new Zend_Form_Element_Select('role_id', array('class' => 'required'));
        $roleId->setRequired(true)
                ->setLabel('Role')
                ->setMultiOptions(Fox::getModel('admin/role')->getUserRole());

        $subForm1->addElements(array($idField, $username, $firstname, $lastname, $email, $password, $cPassword, $contact, $status));
        $subForm2->addElements(array($roleId));
        $form->addSubForm($subForm1, 'subform1');
        $form->addSubForm($subForm2, 'subform2');

        parent::_prepareForm($form);
    }

}