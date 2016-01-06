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
 * Class Fox_Admin_View_System_Email_Template_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Email_Template_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public construct
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('core/email/template');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('admin_email_template')
                ->setMethod('post');
        $id = $this->getRequest()->getParam('id');
        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Email Template Information');
        $subForm1->setDescription('Email Template Information');
        $idField = new Zend_Form_Element_Hidden('id');

        $templateName = new Zend_Form_Element_Select('template_name', array('onchange' => 'getTemplateRecord(this)'));
        $templateName->setRequired(false)
                ->setLabel('Template Name')
                ->setDescription('Choose existing template to load its content')
                ->setMultiOptions(Fox::getModel('core/email/template')->getAllTemplates());

        $name = new Zend_Form_Element_Text('name', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $name->setRequired(true)
                ->setLabel('Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $subject = new Zend_Form_Element_Text('subject', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $subject->setRequired(true)
                ->setLabel('Subject')
                ->addValidator('NotEmpty');

        $content = new Uni_Core_Form_Element_Editor('content', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $content->setRequired(true)
                ->setLabel('Content')
                ->addValidator('NotEmpty');

        if (!$id) {
            $subForm1->addElements(array($idField, $templateName, $name, $subject, $content));
        } else {
            $subForm1->addElements(array($idField, $name, $subject, $content));
        }
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}