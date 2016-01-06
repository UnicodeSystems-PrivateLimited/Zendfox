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
 * Class Fox_Cms_View_Admin_Block_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_View_Admin_Block_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('cmsBlocks');
        $this->setDefaultSort('title');
        $this->setIsExportEnabled(FALSE);
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('cms/block');
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
            'width' => 50,
            'type' => self::TYPE_NUMBER,
            'field' => 'id'
        ));
        $this->addColumn('title', array(
            'label' => 'Title',
            'align' => 'left',
            'field' => 'title'
        ));
        $this->addColumn('identifier_key', array(
            'label' => 'Identifier Key',
            'align' => 'left',
            'field' => 'identifier_key'
        ));
        $this->addColumn('status', array(
            'label' => 'Status',
            'align' => 'center',
            'width' => 100,
            'type' => self::TYPE_OPTIONS,
            'options' => Fox::getModel('cms/block')->getAllStatuses(),
            'field' => 'status'
        ));
        $this->addColumn('created_on', array(
            'label' => 'Created On',
            'align' => 'center',
            'width' => 182,
            'type' => self::TYPE_DATE,
            'field' => 'created_on'
        ));
        $this->addColumn('modified_on', array(
            'label' => 'Modified On',
            'align' => 'center',
            'width' => 182,
            'type' => self::TYPE_DATE,
            'field' => 'modified_on'
        ));

        parent::_prepareColumns();
    }

    /**
     * Get table row url
     * 
     * @param Fox_Cms_Model_Block $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}