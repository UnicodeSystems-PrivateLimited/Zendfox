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
 * Class Fox_Admin_View_System_Cache_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Cache_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('cacheManagement');
        $this->setDefaultSort('status');
        $this->setDefaultDir('DESC');
        $this->setIsExportEnabled(FALSE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('core/cache');
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
        $this->addColumn('cache_type', array(
            'label' => 'Cache',
            'align' => 'left',
            'field' => 'cache_type',
        ));
        $this->addColumn('description', array(
            'label' => 'Description',
            'align' => 'left',
            'field' => 'description',
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('core/cache')->getActiveStatuses(),
            'field' => 'status',
        ));

        parent::_prepareColumns();
    }

    /**
     * Prepare group actions
     */
    function _prepareGroupAction() {
        $this->setGroupActionField('cache_code');
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
                    'multiOptions' => Fox::getModel('core/cache')->getAllStatuses()
                )
            )
        ));
    }

}
