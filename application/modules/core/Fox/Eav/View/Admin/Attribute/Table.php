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
 * Class Fox_Eav_View_Admin_Attribute_Table
 * 
 * @uses Fox_Core_View_Admin_Table
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Attribute_Table extends Fox_Core_View_Admin_Table {

    /**
     * Attribute model object
     * 
     * @var Fox_Eav_Model_Attribute
     */
    protected $attributeModel;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setId('attributes');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('ASC');
        $this->setIsExportEnabled(FALSE);
        $this->setTableSession(TRUE);
        $this->attributeModel = Fox::getModel('eav/attribute');
    }

    /**
     * Prepare model collection
     */
    protected function _prepareCollection() {
        $sortCondition = $this->getSortCondition();
        $filters = $this->getSearchCriteria();
        $collection = $this->attributeModel->getModelCollection($filters, '*', $sortCondition, $this->getColumns());
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
        $this->addColumn('attribute_code', array(
            'label' => 'Attribute Code',
            'align' => 'left',
            'field' => 'attribute_code'
        ));
        $this->addColumn('frontend_label', array(
            'label' => 'Name',
            'align' => 'left',
            'field' => 'frontend_label'
        ));
        $this->addColumn('input_type', array(
            'label' => 'Input Type',
            'align' => 'center',
            'field' => 'input_type',
            'type' => self::TYPE_OPTIONS,
            'options' => $this->attributeModel->getInputTypes()
        ));
        $this->addColumn('is_required', array(
            'label' => 'Required',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => $this->attributeModel->getRequiredStatuses(),
            'field' => 'is_required'
        ));
        $this->addColumn('is_editable', array(
            'label' => 'Editable',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => $this->attributeModel->getEditableStatuses(),
            'field' => 'is_editable'
        ));
        $this->addColumn('is_visible_on_frontend', array(
            'label' => 'Visible',
            'align' => 'center',
            'type' => self::TYPE_OPTIONS,
            'options' => $this->attributeModel->getFrontendVisibleStatuses(),
            'field' => 'is_visible_on_frontend'
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
     * @param Fox_Eav_Model_Attribute $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}