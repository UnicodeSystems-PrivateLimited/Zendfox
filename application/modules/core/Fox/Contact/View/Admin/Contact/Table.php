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
 * Class Fox_Contact_View_Admin_Contact_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Contact
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Contact_View_Admin_Contact_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('contactdetail');
        $this->setDefaultSort('status');
        $this->setIsExportEnabled(TRUE);
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('contact/contact');
        $sortCondition = $this->getSortCondition();
        $filters = $this->getSearchCriteria();
        $collection = $model->getModelCollection($filters, '*', $sortCondition, $this->getColumns());
        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    /**
     * Prepare table columns
     */
    protected function _prepareColumns() {
        $this->addColumn('name', array(
            'label' => 'Name',
            'align' => 'left',
            'field' => 'name',
        ));
        $this->addColumn('subject', array(
            'label' => 'Subject',
            'align' => 'left',
            'field' => 'subject',
        ));
        $this->addColumn('email', array(
            'label' => 'Email',
            'align' => 'left',
            'field' => 'email',
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'width' => 120,
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('contact/contact')->getAllStatuses(),
            'field' => 'status'
        ));
        $this->addColumn('contact_date', array(
            'label' => 'Date',
            'align' => 'center',
            'type' => self::TYPE_DATE,
            'field' => 'contact_date'
        ));
        $this->addColumn('contact_action', array(
            'label' => 'Action',
            'width' => 100,
            'filter' => FALSE,
            'align' => 'center',
            'sorting' => FALSE,
            'field' => 'contact_action',
            'renderer' => 'contact/admin/contact/table/renderer/action'
        ));
        parent::_prepareColumns();
    }

    /**
     * Get table row url
     * 
     * @param Fox_Contact_Model_Contact $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    /**
     * Prepare group actions
     */
    function _prepareGroupAction() {
        $this->setGroupActionField('id');
        $this->addGroupAction('delete', array(
            'label' => 'Delete',
            'url' => Fox::getUrl('*/*/group-delete'),
            'confirm' => 'Are you sure?'
        ));
        $this->addGroupAction('status', array(
            'label' => 'Change status',
            'url' => Fox::getUrl('*/*/group-status'),
            'dependent' => array(
                array(
                    'name' => 'status',
                    'label' => 'Status',
                    'type' => 'select',
                    'options' => array(
                        'class' => 'required'
                    ),
                    'multiOptions' => Fox::getModel('contact/contact')->getAllStatuses()
                )
            )
        ));
    }

}