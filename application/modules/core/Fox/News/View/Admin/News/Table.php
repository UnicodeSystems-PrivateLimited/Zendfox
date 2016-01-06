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
 * Class Fox_News_View_Admin_News_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_News
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_News_View_Admin_News_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('news');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setIsExportEnabled(FALSE);
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('news/news');
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
        $this->addColumn('title', array(
            'label' => 'Title',
            'align' => 'left',
            'field' => 'title'
        ));
        $this->addColumn('date_from', array(
            'label' => 'Date From',
            'align' => 'center',
            'field' => 'date_from',
            'type' => self::TYPE_DATE,
        ));
        $this->addColumn('date_to', array(
            'label' => 'Date To',
            'align' => 'center',
            'field' => 'date_to',
            'type' => self::TYPE_DATE,
        ));
        $this->addColumn('sort_order', array(
            'label' => 'Sort Order',
            'align' => 'center',
            'field' => 'sort_order',
            'width' => 50,
            'type' => self::TYPE_NUMBER,
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'width' => 80,
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('news/news')->getAllStatuses(),
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
                    'multiOptions' => Fox::getModel('news/news')->getAllStatuses()
                )
            )
        ));
    }

    /**
     * Get row url
     * 
     * @param Fox_News_Model_News $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}