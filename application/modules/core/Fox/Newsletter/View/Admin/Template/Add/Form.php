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
 * Class Fox_Newsletter_View_Admin_Template_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Newsletter_View_Admin_Template_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('newsletter/template');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('newsletter_template')
                ->setMethod('post');

        $subForm1 = new Uni_Core_Form_SubForm();
        $subForm1->setLegend('Newsletter Template Information');
        $subForm1->setDescription('Newsletter Template Information');

        $id = new Zend_Form_Element_Hidden('id');

        $name = new Zend_Form_Element_Text('name', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $name->setRequired(true)
                ->setLabel('Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $subject = new Zend_Form_Element_Text('subject', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $subject->setRequired(true)
                ->setLabel('Subject')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $sender_name = new Zend_Form_Element_Text('sender_name', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $sender_name->setRequired(true)
                ->setLabel('Sender Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $sender_email = new Zend_Form_Element_Text('sender_email', array('size' => '30', 'maxlength' => 200, 'class' => 'required email'));
        $sender_email->setRequired(true)
                ->setLabel('Sender Email')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $content = new Uni_Core_Form_Element_Editor('content', array('cols' => '50', 'rows' => '20', 'class' => 'required'));
        $content->setRequired(true)
                ->setLabel('Content');

        $subForm1->addElements(array($id, $name, $subject, $sender_name, $sender_email, $content));
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}

