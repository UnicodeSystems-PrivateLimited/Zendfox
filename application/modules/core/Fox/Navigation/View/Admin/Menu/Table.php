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
 * Class Fox_Navigation_View_Admin_Menu_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Navigation
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Navigation_View_Admin_Menu_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('menuItems');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setIsExportEnabled(FALSE);
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $sortCondition = $this->getSortCondition();
        $filters = $this->getSearchCriteria();
        $model = Fox::getModel('navigation/menu');
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
            'field' => 'id',
            'type' => self::TYPE_NUMBER,
            'width' => 50
        ));
        $this->addColumn('title', array(
            'label' => 'Title',
            'align' => 'left',
            'field' => 'title'
        ));
        $this->addColumn('link', array(
            'label' => 'Link',
            'align' => 'left',
            'field' => 'link'
        ));
        $this->addColumn('open_window', array(
            'label' => 'Target Window',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('navigation/menu')->getAllTargetWindows(),
            'field' => 'open_window'
        ));
        $this->addColumn('sort_order', array(
            'label' => 'Order',
            'align' => 'center',
            'field' => 'sort_order',
            'type' => self::TYPE_NUMBER,
            'width' => 50
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('navigation/menu')->getAllStatuses(),
            'field' => 'status'
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
                    'multiOptions' => Fox::getModel('navigation/menu')->getAllStatuses()
                )
            )
        ));
    }

    /**
     * Get row url
     * 
     * @param Fox_Navigation_Model_Menu $row
     * @return string
     */
    public function getRowUrl($row=NULL) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}