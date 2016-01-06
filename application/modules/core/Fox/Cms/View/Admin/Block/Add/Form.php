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
 * Class Fox_Cms_View_Admin_Block_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_View_Admin_Block_Add_Form extends Fox_Core_View_Admin_Form {
    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('cms/block');
    }
    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('cms_block')
                ->setMethod('post');

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Block Information');
        $subForm1->setDescription('Block Information');

        $idField = new Zend_Form_Element_Hidden('id');
        $title = new Zend_Form_Element_Text('title', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $title->setRequired(true)
                ->setLabel('Title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $identifer = new Zend_Form_Element_Text('identifier_key', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $identifer->setRequired(true)
                ->setLabel('Identifer Key')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $content = new Uni_Core_Form_Element_Editor('content', array('cols' => '50', 'rows' => '20', 'class' => 'required'));
        $content->setRequired(true)
                ->setLabel('Content');

        $status = new Zend_Form_Element_Select('status', array('class' => 'required'));
        $status->setRequired(true)
                ->setLabel('Status') 
                ->setMultiOptions(Fox::getModel('cms/block')->getAllStatuses());
        
        $subForm1->addElements(array($idField, $title, $identifer, $content,$status));
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}