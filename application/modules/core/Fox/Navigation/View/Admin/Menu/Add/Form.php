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
 * Class Fox_Navigation_View_Admin_Menu_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_View_Admin_Menu_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('navigation/menu');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('menu')
                ->setMethod('post');
        $subForm1 = new Zend_Form_SubForm();
        $subForm1->setLegend('Menu Item Information');
        $subForm1->setDescription('Menu Item Information');
        $idField = new Zend_Form_Element_Hidden('id');
        $title = new Zend_Form_Element_Text('title', array('class' => 'required', 'maxlength' => 200));
        $title->setRequired(true)
                ->setLabel('Title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $link = new Zend_Form_Element_Text('link', array('maxlength' => 200));
        $link->setLabel('Link')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setDescription('Use module/controller/action for internal links or http://www.example.com for external links');

        $open_window = new Zend_Form_Element_Select('open_window', array('class' => 'required', 'maxlength' => 200));
        $open_window->setRequired(true)
                ->setLabel('Open Window')
                ->setMultiOptions(Fox::getModel('navigation/menu')->getAllTargetWindows());

        $status = new Zend_Form_Element_Select('status', array('class' => 'required', 'maxlength' => 200));
        $status->setRequired(true)
                ->setLabel('Status')
                ->setMultiOptions(Fox::getModel('navigation/menu')->getAllStatuses());

        $sort_order = new Zend_Form_Element_Text('sort_order', array('class' => 'required', 'maxlength' => 200));
        $sort_order->setRequired(true)
                ->setLabel('Sort Order')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        
        $style_class = new Zend_Form_Element_Text('style_class');
        $style_class->setLabel('Style Class')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $menugroup = new Zend_Form_Element_Multiselect('menu_group', array('class' => 'required'));
        $menugroup->setRequired(true)
                ->setLabel('Menu Group')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setMultiOptions(Fox::getModel('navigation/menugroup')->getMenuGroupOptions());

        $subForm1->addElements(array($idField, $title, $link, $open_window, $sort_order, $style_class, $status, $menugroup));
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}