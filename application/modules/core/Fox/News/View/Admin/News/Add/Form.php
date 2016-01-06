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
 * Class Fox_News_View_Admin_News_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_News
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_News_View_Admin_News_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('news/news');
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('news')
                ->setMethod('post');

        $subForm1 = new Uni_Core_Form_SubForm();
        $subForm1->setLegend('News Information');
        $subForm1->setDescription('News Information');

        $idField = new Zend_Form_Element_Hidden('id');
        $title = new Zend_Form_Element_Text('title', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $title->setRequired(true)
                ->setLabel('Title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $content = new Uni_Core_Form_Element_Editor('content', array('cols' => '50', 'rows' => '20', 'class' => 'required'));
        $content->setRequired(true)
                ->setLabel('Content');

        $dateFrom = new Uni_Core_Form_Element_Date('date_from');
        $dateFrom->setLabel('Date From')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setDescription('Must Be less than Date To')
                ->addValidator('NotEmpty');

        $dateTo = new Uni_Core_Form_Element_Date('date_to');
        $dateTo->setLabel('Date To')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $sort_order = new Zend_Form_Element_Text('sort_order', array('size' => '30', 'maxlength' => 200, 'class' => 'integer'));
        $sort_order->setLabel('Sort Order')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $status = new Zend_Form_Element_Select('status', array('class' => 'required'));
        $status->setRequired(true)
                ->setLabel('Status')
                ->setMultiOptions(Fox::getModel('news/news')->getAllStatuses());

        $subForm1->addElements(array($idField, $title, $dateFrom, $dateTo, $content, $sort_order, $status));
        $form->addSubForm($subForm1, 'subform1');
        parent::_prepareForm($form);
    }

}