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
 * Class Fox_Admin_View_System_Email_Template_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_System_Email_Template_Table extends Fox_Core_View_Admin_Table {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('emailTemplate');
        $this->setDefaultSort('id');
        $this->setIsExportEnabled(FALSE);
        $this->setTableSession(TRUE);
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $model = Fox::getModel('core/email/template');
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
            'field' => 'id',
            'type' => self::TYPE_NUMBER,
            'width' => '50'
        ));
        $this->addColumn('name', array(
            'label' => 'Name',
            'align' => 'left',
            'field' => 'name'
        ));
        $this->addColumn('subject', array(
            'label' => 'Subject',
            'align' => 'left',
            'field' => 'subject'
        ));
        $this->addColumn('created_date', array(
            'label' => 'Created Date',
            'align' => 'center',
            'field' => 'created_date',
            'type' => self::TYPE_DATE,
            'width' => '80'
        ));
        $this->addColumn('modified_date', array(
            'label' => 'Modified Date',
            'align' => 'center',
            'field' => 'modified_date',
            'type' => self::TYPE_DATE,
            'width' => '80'
        ));

        parent::_prepareColumns();
    }

    /**
     * Get table row url
     * 
     * @param Fox_Core_Model_Email_Template $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
