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
 * Class Fox_Core_View_Admin_Table
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Table extends Uni_Core_Admin_View {
    const TYPE_TEXT='text';
    const TYPE_OPTIONS='options';
    const TYPE_DATE='date';
    const TYPE_NUMBER='number';
    const EXPORT_TYPE_CSV='csv';
    const EXPORT_TYPE_XML='xml';
    /**
     * @var string
     */
    protected $id;
    /**
     * Model object collection
     * 
     * @var Uni_Core_Model_Collection
     */
    protected $collection;
    /**
     * Sort field
     * 
     * @var string
     */
    protected $defaultSort;
    /**
     * Sort direction
     * 
     * @var string
     */
    protected $defaultDir = 'ASC';
    /**
     * Page number
     * 
     * @var int
     */
    protected $defaultPageNumber = 1;
    /**
     * Record count per page
     * 
     * @var int
     */
    protected $defaultRecordCount = 10;
    /**
     * Table columns
     * 
     * @var array
     */
    protected $columns = array();
    /**
     * Group actions
     * 
     * @var array
     */
    protected $groupActions = array();
    /**
     * Group action field
     * 
     * @var string
     */
    protected $groupActionField;
    /**
     * Item count per page options
     * 
     * @var array
     */
    protected $itemCountOptions = array(2, 5, 10, 20, 30, 50, 100, 150, 200);
    /**
     * Request filters
     * 
     * @var array|NULL
     */
    protected $params = NULL;
    /**
     * Enable session for table
     * 
     * @var boolean
     */
    protected $tableSession = FALSE;
    /**
     * Enable export feature
     * 
     * @var boolean
     */
    protected $isExportEnabled = FALSE;
    /**
     * Action name for export feature
     * 
     * @var string
     */
    protected $exportButtonAction = 'export';

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('fox/containers/table');
    }

    /**
     * Get table id
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set table id
     * 
     * @param string $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get table session
     * 
     * @return boolean
     */
    protected function getTableSession() {
        if (!$this->id) {
            $this->setId(get_class($this));
        }
        return ($this->tableSession);
    }

    /**
     * Set table session
     * 
     * @param boolean $session 
     */
    protected function setTableSession($session) {
        $this->tableSession = $session;
    }

    /**
     * Get model object collection
     * 
     * @return Uni_Core_Model_Collection
     */
    public function getCollection() {
        return $this->collection;
    }

    /**
     * Set model object collection
     * 
     * @param Uni_Core_Model_Collection $collection 
     */
    public function setCollection($collection) {
        $this->collection = $collection;
    }

    /**
     * Get default sort field
     * 
     * @return string
     */
    public function getDefaultSort() {
        return $this->defaultSort;
    }

    /**
     * Set default sort field
     * 
     * @param string $defaultSort 
     */
    public function setDefaultSort($defaultSort) {
        $this->defaultSort = $defaultSort;
    }

    /**
     * Get default sort direction
     * 
     * @return string
     */
    public function getDefaultDir() {
        return $this->defaultDir;
    }

    /**
     * Set default sort direction
     * 
     * @param string $defaultDir 
     */
    public function setDefaultDir($defaultDir) {
        $this->defaultDir = $defaultDir;
    }

    /**
     * Get default page number
     * 
     * @return int
     */
    public function getDefaultPageNumber() {
        return $this->defaultPageNumber;
    }

    /**
     * Set default page number
     * 
     * @param int $defaultPageNumber 
     */
    public function setDefaultPageNumber($defaultPageNumber) {
        $this->defaultPageNumber = $defaultPageNumber;
    }

    /**
     * Get default record count per page
     * 
     * @return int
     */
    public function getDefaultRecordCount() {
        return $this->defaultRecordCount;
    }

    /**
     * Set default record count per page
     * 
     * @param int $defaultRecordCount 
     */
    public function setDefaultRecordCount($defaultRecordCount) {
        $this->defaultRecordCount = $defaultRecordCount;
    }

    /**
     * Get sort field
     * 
     * @return string
     */
    public function getSort() {
        $sortField = $this->defaultSort;
        if (($sort = $this->getRequest()->getParam('sort'))) {
            $sortField = $sort;
        } else if ($this->getTableSession()) {
            $filters = $this->getSessionFilters();
            if (isset($filters['sort'])) {
                $sortField = $filters['sort'];
            }
        }
        return $sortField;
    }

    /**
     * Get sort direction
     * 
     * @return string
     */
    public function getDir() {
        $sortDir = $this->defaultDir;
        if (($dir = $this->getRequest()->getParam('dir'))) {
            $sortDir = $dir;
        } else if ($this->getTableSession()) {
            $filters = $this->getSessionFilters();
            if (isset($filters['dir'])) {
                $sortDir = $filters['dir'];
            }
        }
        return $sortDir ? $sortDir : 'ASC';
    }

    /**
     * Get sort contdition
     * 
     * @return array
     */
    public function getSortCondition() {
        $sortCondition = array();
        $sort = $this->getSort();
        if ($sort) {
            $dir = $this->getDir();
            $sortCondition[$sort] = $dir;
        }
        return $sortCondition;
    }

    /**
     * Get search criteria
     * 
     * @return mixed
     */
    public function getSearchCriteria() {
        $searchFilters = '';
        $isReset = FALSE;
        if ($this->isAjax()) {
            $filters = $this->getRequest()->getParam('filters', NULL);
            if (!$filters) {
                $isReset = TRUE;
                $searchFilters = NULL;
                $this->clearSearchCriteria();
            }
        }
        if (!$isReset) {
            if (($filters = $this->getRequest()->getParam('filters', NULL))) {
                $searchFilters = $filters;
            } else if ($this->getTableSession()) {
                $filters = $this->getSessionFilters();
                if (isset($filters['filters'])) {
                    $searchFilters = $filters['filters'];
                }
            }
        }
        return $searchFilters;
    }

    /**
     * Get item count per page options
     * 
     * @return array
     */
    public function getItemCountOptions() {
        return $this->itemCountOptions;
    }

    /**
     * Set item count per page options
     * 
     * @param array $itemCountOptions 
     */
    public function setItemCountOptions($itemCountOptions) {
        $this->itemCountOptions = $itemCountOptions;
    }

    /**
     * Prepare collection
     */
    protected function _prepareCollection() {
        
    }

    /**
     * Get row url
     * 
     * @param object|NULL $row
     * @return string
     */
    protected function getRowUrl($row=NULL) {
        return '';
    }

    /**
     * Retrieve whether the request is by ajax call
     * 
     * @return boolean
     */
    protected function isAjax() {
        return (boolean) $this->getRequest()->getParam('isAjax');
    }

    /**
     * Get group actions
     * 
     * @return array
     */
    public function getGroupActions() {
        return $this->groupActions;
    }

    /**
     * Add group action
     * 
     * @param string $action action label
     * @param string $actionUrl action url
     */
    public function addGroupAction($action, $actionUrl) {
        $this->groupActions[$action] = $actionUrl;
    }

    /**
     * Returns selected records
     * 
     * @return array
     */
    public function getSelection() {
        return array('selected' => $this->getRequest('selected', FALSE), 'extra' => $this->getRequest('extra', array()));
    }

    /**
     * Get table ajax action url
     * 
     * @return string 
     */
    public function getAjaxUrl() {
        return Fox::getUrl('*/*/ajax-table');
    }

    /**
     * Retrieve whether the export feature is enabled
     * 
     * @return boolean
     */
    public function getIsExportEnabled() {
        return $this->isExportEnabled;
    }

    /**
     * Enable|Diable export feature
     * 
     * @param boolean $isExportEnabled 
     */
    public function setIsExportEnabled($isExportEnabled) {
        $this->isExportEnabled = $isExportEnabled;
    }

    /**
     * Retrieve export button action
     * 
     * @return string
     */
    public function getExportButtonAction() {
        return $this->exportButtonAction;
    }

    /**
     * Set action for export button
     * 
     * @param string $exportButtonAction 
     */
    public function setExportButtonAction($exportButtonAction) {
        $this->exportButtonAction = $exportButtonAction;
    }

    /**
     * Get url for export
     * 
     * @return string 
     */
    public function getExportButtonUrl() {
        return Fox::getUrl('*/*/' . $this->exportButtonAction);
    }

    /**
     * Gets value of field on which the group action is to be performed
     *
     * @param Uni_Core_Model $item
     * @return string
     */

    public function getGroupActionValue($item) {
        if (isset($this->groupActionField)) {
            $method = 'get' . ucfirst(Uni_Core_Model::getPropertyName($this->groupActionField));
            $value = $item->$method;
        } else {
            $value = $item->getId();
        }
        return $value;
    }

    /**
     * Set the field on which the group action will be applied
     * 
     * @param string $groupActionField
     */
    public function setGroupActionField($groupActionField) {
        $this->groupActionField = $groupActionField;
    }

    /**
     * Returns the filter and sort condition as json string
     * 
     * @return string json data
     */
    public function getParamsJson() {
        $this->params = $this->getSessionFilters();
        if (($filters = $this->getRequest()->getParam('filters'))) {
            $this->params['filters'] = $filters;
        }
        $sort = $this->getSort();
        $dir = $this->getDir();
        if ($sort) {
            $this->params['sort'] = $sort;
            $this->params['dir'] = $dir;
        }
        $selected = $this->getRequest()->getParam('selected', NULL);
        if (!is_null($selected)) {
            $this->params['selected'] = intval($selected);
        }
        if (($extra = $this->getRequest()->getParam('extra', FALSE))) {
            $this->params['extra'] = $extra;
        }
        $this->setSessionFilters($this->params);
        return empty($this->params) ? '{}' : Zend_Json_Encoder::encode($this->params);
    }

    /**
     * Prepare table columns
     */
    protected function _prepareColumns() {
        $columnsPrep = array();
        if (!empty($this->columns)) {
            if (!empty($this->groupActions)) {
                $columnsPrep['gr_act_' . $this->groupActionField] = $this->getPreparedColumn(array(
                            'label' => '',
                            'align' => 'center',
                            'field' => $this->groupActionField,
                            'width' => 20,
                            'class' => 'groupaction_check',
                            'filter' => FALSE,
                            'renderer' => 'core/admin/table/renderer/checkbox'
                        ));
            }
            foreach ($this->columns as $k => $column) {
                $columnsPrep[$k] = $this->getPreparedColumn($column);
            }
            $this->columns = $columnsPrep;
            unset($columnsPrep);
        }
        if (NULL == $this->id) {
            $this->setId(get_class($this));
        }
    }

    /**
     * Prepares column array data to ready to use in data table rendering
     *
     * @param array $column
     * @return array 
     */

    function getPreparedColumn($column) {
        $words = explode('_', $column['field']);
        $mName = '';
        foreach ($words as $word) {
            $mName = $mName . ucfirst($word);
        }
        $column['fieldMethod'] = 'get' . $mName;
        if (!isset($column['type'])) {
            $column['type'] = self::TYPE_TEXT;
        }
        if (isset($column['filter'])) {
            if ($column['filter']) {
                $column['filterMarkup'] = $this->_getFilterMarkup($column);
            } else {
                $column['filterMarkup'] = '&nbsp;';
            }
        } else {
            $column['filterMarkup'] = $this->_getFilterMarkup($column);
        }
        return $column;
    }

    /**
     * Gets html contents to create filter section in table view
     * 
     * @param array $column
     * @return string
     */

    protected function _getFilterMarkup($column) {
        if ($column['type'] == self::TYPE_TEXT) {
            $markUp = $this->formText('filters[' . $column['field'] . ']', '', array('class' => 'filter text'));
        } else if ($column['type'] == self::TYPE_OPTIONS) {
            $markUp = $this->formSelect('filters[' . $column['field'] . ']', '', array('class' => 'filter select'), $column['options']);
        } else if ($column['type'] == self::TYPE_DATE) {
            $markUp = '<div class="range"><div class="range_row"><span>From:</span>' . $this->formDate('filters[' . $column['field'] . '][from]', '', array('class' => 'filter text')) . '</div><div class="range_row"><span>To:</span>' . $this->formDate('filters[' . $column['field'] . '][to]', '', array('class' => 'filter text')) . '</div></div>';
        } else if ($column['type'] == self::TYPE_NUMBER) {
            $markUp = '<div class="range"><div class="range_row"><span>From:</span>' . $this->formText('filters[' . $column['field'] . '][from]', '', array('class' => 'filter text')) . '</div><div class="range_row"><span>To:</span>' . $this->formText('filters[' . $column['field'] . '][to]', '', array('class' => 'filter text')) . '</div></div>';
        } else {
            $markUp = '';
        }
        return $markUp;
    }

    /**
     * Prepare table
     */
    private function _prepareTable() {
        $request = $this->getRequest();
        $page = $request->getParam('page');
        $recCount = $request->getParam('recCount');
        $paging = array('currentPage' => $this->defaultPageNumber, 'recordCount' => $this->defaultRecordCount);
        if (!$page && !$recCount && $this->getTableSession()) {
            $paging = $this->getPagingFilters();
        } else if ($page || $recCount) {
            if ($page) {
                $paging['currentPage'] = $page;
            }
            if ($recCount) {
                $paging['recordCount'] = $recCount;
            }
        }
        $this->collection->setCurrentPageNumber($paging['currentPage']);
        $this->collection->setItemCountPerPage($paging['recordCount']);
        $this->setPagingFilters($paging);
    }

    /**
     * Prepare group action
     * 
     * @return void
     */
    function _prepareGroupAction() {
        
    }

    /**
     * Adds new column in table display
     *
     * @param string $key
     * @param array $column 
     * @return void
     */

    public function addColumn($key, array $column) {
        $this->columns[$key] = $column;
    }

    /**
     * Get table columns
     * 
     * @return array 
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Renders and generates html contents for table cell
     *
     * @param Uni_Core_Model $item
     * @param array $column
     * @return string HTML contents to generate a single cell 
     */

    public function renderTableCell($item, $column) {
        if (array_key_exists('renderer', $column) && ($renderer = Fox::getView($column['renderer']))) {
            if (!$renderer instanceof Uni_Core_Renderer_Interface) {
                throw new Exception('Invalid Renderer! must be object of Uni_Core_Renderer_Interface');
            }
            return $renderer->render($item->{$column['fieldMethod']}());
        }
        return Fox::getView('core/admin/table/renderer/column')->renderTableColumn($item, $column);
    }

    /**
     * Render contents
     * 
     * @return string 
     */
    public function renderView() {
        $this->_prepareGroupAction();
        $this->_prepareColumns();
        $this->_prepareCollection();
        $this->_prepareTable();
        return parent::renderView();
    }

    /**
     * Callback to use within sorting columns in proper order
     *
     * @param array $item1
     * @param array $item2
     * @return int 
     */

    public static function compareOrder($item1, $item2) {
        $order1 = isset($item1['order']) ? $item1['order'] : 0;
        $order2 = isset($item2['order']) ? $item2['order'] : 0;
        return (($order1 == $order2) ? 0 : (($order1 < $order2) ? -1 : 1));
    }

    /**
     * Set filter and sort condition in session
     * 
     * @param string $filters json data
     * @return void
     */
    private function setSessionFilters($filters) {
        if ($this->getTableSession()) {
            $session = Fox::getModel('admin/session');
            $filterArr = $session->getSearchFilters();
            $filterArr[$this->id] = $filters;
            $session->setSearchFilters($filterArr);
        }
    }

    /**
     * Return filters
     * 
     * @return mixed
     */
    private function getSessionFilters() {
        $filters = $this->params;
        if ($this->getTableSession()) {
            $session = Fox::getModel('admin/session');
            $filterArr = $session->getSearchFilters();
            if (is_array($filterArr) && isset($filterArr[$this->id])) {
                $filters = $filterArr[$this->id];
            }
            if (isset($filterArr[$this->id]['filters']) && !$filterArr[$this->id]['filters']) {
                unset($filterArr[$this->id]['filters']);
            }
        }
        return $filters;
    }

    /**
     * Set pagination info in session
     * 
     * @param array $paging 
     * @return void
     */
    private function setPagingFilters($paging) {
        if ($this->getTableSession()) {
            $session = Fox::getModel('admin/session');
            $filterArr = $session->getSearchFilters();
            $filterArr[$this->id]['paging'] = $paging;
            $session->setSearchFilters($filterArr);
        }
    }

    /**
     * Get pagination info
     * 
     * @return array 
     */
    private function getPagingFilters() {
        $paging = array('currentPage' => $this->defaultPageNumber, 'recordCount' => $this->defaultRecordCount);
        if ($this->getTableSession()) {
            $session = Fox::getModel('admin/session');
            $filterArr = $session->getSearchFilters();
            if (is_array($filterArr) && isset($filterArr[$this->id]) && isset($filterArr[$this->id]['paging'])) {
                $paging = $filterArr[$this->id]['paging'];
            }
        }
        return $paging;
    }

    /**
     * Clear search filters
     * 
     * @return void
     */
    private function clearSearchCriteria() {
        if ($this->getTableSession()) {
            $session = Fox::getModel('admin/session');
            $filterArr = $session->getSearchFilters();
            if (isset($filterArr[$this->id]) && isset($filterArr[$this->id]['filters'])) {
                unset($filterArr[$this->id]['filters']);
            }
            $session->setSearchFilters($filterArr);
        }
    }

    /**
     * Exports data to csv
     * 
     * @param string $fileName 
     * @return void
     */
    public function exportToCSV($fileName) {
        if (!$fileName) {
            $fileName = 'export-' . time() . '.csv';
        }
        $output = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        $fieldsMetaData = array();
        $headerColumns = array();
        $collection = $this->getCollection();
        foreach ($collection as $row) {
            $row->load($row->getId());
            $fieldsMetaData = $row->getTableMetaData();
            break;
        }
        foreach ($this->columns as $fieldKey => $column) {
            if ($column['label'] && isset($fieldsMetaData[$fieldKey])) {
                $headerColumns[] = $column['label'];
            }
        }
        fputcsv($output, $headerColumns);
        foreach ($collection as $row) {
            $data = array();
            foreach ($this->columns as $field) {
                if ($field['label'] && isset($fieldsMetaData[$field['field']])) {
                    $data[$field['label']] = $row->{$field['fieldMethod']}();
                }
            }
            fputcsv($output, $data);
        }
        rewind($output);
        $export = stream_get_contents($output);
        fclose($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $export;
    }

    /**
     * Exports data to xml
     * 
     * @param string $fileName 
     * @return void
     */
    public function exportToXML($fileName) {
        if (!$fileName) {
            $fileName = 'export-' . time() . '.xml';
        }
        $doc = new DomDocument();
        $doc->preserveWhiteSpace = FALSE;
        $doc->formatOutput = TRUE;
        $root = $doc->createElement('Table');
        $root = $doc->appendChild($root);
        $fieldsMetaData = array();
        $collection = $this->getCollection();
        foreach ($collection as $row) {
            if (empty($fieldsMetaData)) {
                $row->load($row->getId());
                $fieldsMetaData = $row->getTableMetaData();
            }
            $rows = $doc->createElement('Row');
            $rows = $root->appendChild($rows);
            foreach ($this->columns as $field) {
                if (isset($fieldsMetaData[$field['field']])) {
                    $column = $doc->createElement('Column');
                    $column->setAttribute('name', $fieldsMetaData[$field['field']]['COLUMN_NAME']);
                    $column->setAttribute('type', $fieldsMetaData[$field['field']]['DATA_TYPE']);
                    $cData = $doc->createCDATASection('');
                    $cData->appendData($row->{$field['fieldMethod']}());
                    $column->appendChild($cData);
                    $rows->appendChild($column);
                }
            }
        }
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $doc->saveXML();
    }

}