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
 * Class Fox_Member_View_Admin_Member_Password_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Admin_Member_Password_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('member/member');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('change_password')
                ->setMethod('post');

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Change Member Password');
        $subForm1->setDescription('Change Member Password');

        $idField = new Zend_Form_Element_Hidden('id');

        $emailFieldOptions=array('size'=>'30','maxlength'=>200,'class'=>'required email');
        if($this->getRequest()->getParam('id') && Fox::getModel('member/member')->load($this->getRequest()->getParam('id'))){
            $emailFieldOptions['readonly']='readonly';
        }
        $emailId = new Zend_Form_Element_Text('email_id', $emailFieldOptions);
        $emailId->setRequired(true)
                ->setLabel('Email Id')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $password = new Zend_Form_Element_Password('password', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $password->setRequired(true)
                ->setLabel('Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $cPassword = new Zend_Form_Element_Password('c_password', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $cPassword->setRequired(true)
                ->setLabel('Confirm Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $subForm1->addElements(array($idField, $emailId, $password, $cPassword));
        $form->addSubForm($subForm1, 'subform1');

        parent::_prepareForm($form);
    }

}