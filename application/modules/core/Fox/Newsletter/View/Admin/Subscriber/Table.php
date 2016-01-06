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
 * Class Fox_Newsletter_View_Admin_Subscriber_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Newsletter_View_Admin_Subscriber_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('subscribers');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('newsletter/subscriber');
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
            'field' => 'id',
        ));
        $this->addColumn('name', array(
            'label' => 'Name',
            'align' => 'left',
            'field' => 'name',
        ));
        $this->addColumn('email', array(
            'label' => 'Email',
            'align' => 'left',
            'field' => 'email',
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'width' => 150,
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('newsletter/subscriber')->getAllStatuses(),
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
            'label' => 'Unsubscribe',
            'url' => Fox::getUrl('*/*/group-unsubscribe')
        ));
    }

}