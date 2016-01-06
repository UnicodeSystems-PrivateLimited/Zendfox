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
 * Class Fox_Eav_View_Admin_Set_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Set_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('attributeSets');
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
        $setModel = Fox::getModel('eav/set');
        $collection = $setModel->getModelCollection($filters, '*', $sortCondition, $this->getColumns());
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
            'field' => 'id',
            'width' => 50
        ));
        $this->addColumn('attribute_set_name', array(
            'label' => 'Name',
            'align' => 'left',
            'field' => 'attribute_set_name'
        ));
        parent::_prepareColumns();
    }

    /**
     * Prepare group action
     */
    function _prepareGroupAction() {
        $this->setGroupActionField('id');
        $this->addGroupAction('delete', array(
            'label' => 'Delete',
            'url' => Fox::getUrl('*/*/group-delete'),
            'confirm' => 'Are you sure?'
        ));
    }

    /**
     * Get row url
     * 
     * @param Fox_Eav_Model_Set $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}