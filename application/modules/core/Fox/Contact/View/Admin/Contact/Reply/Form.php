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
 * Class Fox_Contact_View_Admin_Contact_Reply_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Contact
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Contact_View_Admin_Contact_Reply_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('contact/contact');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('contact_reply')
                ->setMethod('post');

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Contact Information');
        $subForm1->setDescription('Contact Information');

        $idField = new Zend_Form_Element_Hidden('id');


        $name = new Zend_Form_Element_Text('name', array('size' => '30', 'maxlength' => 200, 'class' => 'required', 'readonly' => 'readonly'));
        $name->setRequired(true)
                ->setLabel('Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $email = new Zend_Form_Element_Text('email', array('size' => '30', 'maxlength' => 200, 'class' => 'email required', 'readonly' => 'readonly'));
        $email->setRequired(true)
                ->setLabel('Email')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $subject = new Zend_Form_Element_Text('subject', array('size' => '30', 'maxlength' => 200, 'class' => 'required', 'readonly' => 'readonly'));
        $subject->setRequired(true)
                ->setLabel('Subject')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $message = new Zend_Form_Element_Textarea('message', array('cols' => '30', 'rows' => '5', 'class' => 'required', 'readonly' => 'readonly'));
        $message->setRequired(true)
                ->setLabel('Message')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $reply = new Zend_Form_Element_Textarea('reply_message', array('cols' => '30', 'rows' => '5', 'class' => 'required'));
        $reply->setRequired(true)
                ->setLabel('Reply Message')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $subForm1->addElements(array($idField, $name, $subject, $email, $message, $reply));
        $form->addSubForm($subForm1, 'subform1');

        parent::_prepareForm($form);
    }

}