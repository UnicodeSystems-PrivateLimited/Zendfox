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
 * Class Fox_Member_View_Admin_Member_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Admin_Member_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('memberdetail');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setTableSession(TRUE);
        $this->setIsExportEnabled(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('member/member');
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
        $this->addColumn('id', array(
            'label' => 'Id',
            'align' => 'left',
            'type' => self::TYPE_NUMBER,
            'width' => 50,
            'field' => 'id'
        ));
        $this->addColumn('first_name', array(
            'label' => 'First Name',
            'align' => 'left',
            'field' => 'first_name'
        ));
        $this->addColumn('last_name', array(
            'label' => 'Last Name',
            'align' => 'left',
            'field' => 'last_name'
        ));
        $this->addColumn('email_id', array(
            'label' => 'Email Id',
            'align' => 'left',
            'field' => 'email_id'
        ));
        $this->addColumn('join_date', array(
            'label' => 'Join Date',
            'align' => 'center',
            'field' => 'join_date',
            'type' => self::TYPE_DATE,
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'width' => 100,
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('member/member')->getAllStatuses(),
            'field' => 'status'
        ));
        $this->addColumn('member_action', array(
            'label' => 'Action',
            'width' => 120,
            'filter' => FALSE,
            'sorting' => FALSE,
            'align' => 'center',
            'field' => 'member_action',
            'renderer' => 'member/admin/member/table/renderer/action'
        ));

        parent::_prepareColumns();
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
                    'multiOptions' => Fox::getModel('member/member')->getGroupStatuses()
                )
            )
        ));
    }

    /**
     * Get row url
     * 
     * @param Fox_Member_Model_Member $row
     * @return string
     */
    public function getRowUrl($row=NULL) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}