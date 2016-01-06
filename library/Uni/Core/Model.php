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
 * Class Uni_Core_Model
 * provides core functionality of models
 * 
 * @uses        Zend_Db_Table_Abstract
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Model extends Zend_Db_Table_Abstract {
    /*     * #@+
     * method type constants
     */
    const GET='get';
    const SET='set';
    /*     * #@- */

    /**
     *
     * @var string name of table 
     */
    protected $_name;
    /**
     *
     * @var string primary key field of table
     */
    protected $primary = 'id';
    /**
     * Data stored by current object
     *
     * @var array 
     */
    protected $data = array();

    /**
     * Constructs object of model using optional configuration
     * supplied by $config param
     *
     * @param array $config 
     */
    public function __construct($config=array()) {
        parent::__construct($config);
    }

    /**
     * 
     * @return void
     */
    protected function _setupTableName() {
        parent::_setupTableName();
        $this->_name = Fox::getTablePrefix() . $this->_name;
    }

    /**
     * Sets table name
     *
     * @param string $tableName 
     * @return void
     */
    protected function setTable($tableName) {
        $this->_name = $tableName;
        $this->_setupTableName();
    }

    /**
     *
     * @return string 
     */
    public function getPrimaryField() {
        return $this->primary;
    }

    /**
     * Sets data for current object
     *
     * @param array $data
     * @return Uni_Core_Model 
     */
    public function setData(array $data) {
        foreach ($data as $key => $value) {
            $words = explode('_', $key);
            $mName = '';
            foreach ($words as $word) {
                $mName = $mName . ucfirst($word);
            }
            $method = self::SET . ucfirst($mName);
            $this->$method($value);
        }
        return $this;
    }

    /**
     * Gets data as associative array for fields passed as array using param $fields
     *
     * @param array $fields
     * @return array 
     */
    public function getData(array $fields=NULL) {
        if($fields===NULL){
            $fields = $this->_getCols();
        }
        $data = array();
        foreach ($fields as $key) {
            $value = NULL;
            $property = self::getPropertyName($key);
            if (array_key_exists($property, $this->data)) {
                $method = self::GET . ucfirst($property);
                $value = $this->$method();
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Sets primary field name for current table
     *
     * @param string $primaryKey 
     * @return void
     */
    protected function setPrimaryKey($primaryKey) {
        $this->primary = $primaryKey;
    }

    /**
     *
     * @return string 
     */
    public function getPrimaryKey() {
        return $this->primary;
    }

    /**
     * Get the last executed query
     *
     * @return mixed 
     */
    public function getQueryString() {
        if (($profiler = $this->_db->getProfiler()->getLastQueryProfile())) {
            return $profiler->getQuery();
        }
        return FALSE;
    }

    /**
     * Loads record and sets data in the model for passed criteria
     *
     * @param string $value
     * @param string $field
     * @return object|NULL
     */
    public function load($value, $field=NULL) {
        $condition = '';
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $condition.= ( $condition != '' ? (' AND ' . $this->getAdapter()->quoteIdentifier($key) . "='$val'") : ($this->getAdapter()->quoteIdentifier($key) . "='$val'"));
            }
        } else {
            $condition = ($field ? $this->getAdapter()->quoteIdentifier($field) : $this->getAdapter()->quoteIdentifier($this->primary)) . '=' . "'$value'";
        }
        $record = $this->fetchRow($condition);
        if ($record) {
            return $this->setData($record->toArray());
        }
        return NULL;
    }

    /**
     * Information of describe table method
     *
     * @return array 
     */
    public function getTableMetaData() {
        return $this->_metadata;
    }

    /**
     * Calls insertRecord method insternally additionaly loads saved record in to model
     *
     * @return Uni_Core_Model 
     */
    public function save() {
        $id = $this->insertRecord();
        return $this->load($id);
    }

    /**
     * Inserts if no record already exists in table
     * updates if record already exists in table
     *
     * @return string 
     */
    public function insertRecord() {
        $fields = $this->_getCols();
        $data = $this->getData($fields);
        $id = NULL;
        $data = $this->_preSave($data);
        if (get_magic_quotes_gpc()) {
            $data = $this->manageSpecialQuotes($data);
        }
        if (is_array($data) && array_key_exists($this->primary, $data)) {
            if ($this->load($data[$this->primary])) {
                $id = $data[$this->primary];
            } else {
                $id = NULL;
            }
            $this->unsetData();
        }
        if ($id) {
            $this->update($data, "$this->primary='$id'");
        } else {
            $this->insert(array_filter($data, 'Uni_Core_Model::removeNull'));
            $id = $this->getAdapter()->lastInsertId($this->_name);
            $data[$this->primary] = $id;
        }
        $this->_postSave($data);
        return $id;
    }

    /**
     *
     * @param string $field
     * @return boolean 
     */
    public static function removeNull($field) {
        return!is_null($field);
    }

    /**
     * Get data collection
     * 
     * @param mixed $condition
     * @param mixed $cols
     * @param mixed $sortOrder
     * @param int $limit
     * @param int $pageNumber
     * @return Uni_Core_Model_Collection 
     */
    public function getCollection($condition=null, $cols='*', $sortOrder=NULL, $limit=NULL, $pageNumber=NULL) {
        $select = $this->getAdapter()->select(FALSE)->from(array('mainTable' => $this->_name), $cols);
        if ($condition) {
            if (is_array($condition)) {
                foreach ($condition as $key => $value) {
                    if (is_array($value) && isset($value['operator'])) {
                        $select->where($this->getAdapter()->quoteIdentifier($key) . $value['operator'] . '?', $value['value']);
                    } else {
                        $select->where($this->getAdapter()->quoteIdentifier($key) . "=?", $value);
                    }
                }
            } else {
                $select->where($condition);
            }
        }
        if ($sortOrder) {
            $sortCondition = '';
            if (is_array($sortOrder)) {
                foreach ($sortOrder as $sortKey => $sortVal) {
                    $sortCondition[] = $this->getAdapter()->quoteIdentifier($sortKey) . ' ' . $sortVal;
                }
            } else {
                $sortCondition = $sortOrder;
            }
            $select->order($sortCondition);
        }
        $collection = Uni_Core_Model_Collection::getInstance($select, NULL);
        if ($limit) {
            $collection->setItemCountPerPage($limit);
        } else {
            $collection->setItemCountPerPage($collection->getTotalItemCount());
        }
        if ($pageNumber) {
            $collection->setCurrentPageNumber($pageNumber);
        }
        return $collection;
    }

    /**
     * Get object collection
     *
     * @param mixed $condition
     * @param mixed $cols
     * @param mixed $sortOrder
     * @param array $filterColumns
     * @return Uni_Core_Model_Collection 
     */
    public function getModelCollection($condition=NULL, $cols='*', $sortOrder=NULL, $filterColumns=NULL) {
        $modelClass = get_class($this);
        $select = $this->getAdapter()->select(FALSE)->from(array('mainTable' => $this->_name), $cols);
        if (!empty($condition)) {
            if (is_array($condition)) {
                foreach ($condition as $key => $value) {
                    if (is_array($value) && isset($value['operator'])) {
                        $select->where($this->getAdapter()->quoteIdentifier($key) . $value['operator'] . '?', $value['value']);
                    } else if (isset($filterColumns[$key])) {
                        if (isset($filterColumns[$key]['type'])) {
                            if ($filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_NUMBER) {
                                if (isset($condition[$key]['from']) && $condition[$key]['from']!='') {
                                    $select->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . ">=?", $condition[$key]['from']);
                                }
                                if (isset($condition[$key]['to']) && $condition[$key]['to']!='') {
                                    $select->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . "<=?", $condition[$key]['to']);
                                }
                            } else if ($filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_DATE) {
                                if (isset($condition[$key]['from']) && $condition[$key]['from']!='') {
                                    $select->where((isset($filterColumns[$key]['index']) ? ('date(' . $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) . ')') : ('date(' . $this->getAdapter()->quoteIdentifier($key)) . ')') . ">=?", $condition[$key]['from']);
                                }
                                if (isset($condition[$key]['to']) && $condition[$key]['to']!='') {
                                    $select->where((isset($filterColumns[$key]['index']) ? ('date(' . $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) . ')') : ('date(' . $this->getAdapter()->quoteIdentifier($key)) . ')') . "<=?", $condition[$key]['to']);
                                }
                            } else if (isset($filterColumns[$key]['type']) && $filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_OPTIONS) {
                                $select->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . "=?", $value);
                            } else {
                                $select->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . " like ?", '%' . $value . '%');
                            }
                        }
                    } else {
                        $select->where($this->getAdapter()->quoteIdentifier($key) . "=?", $value);
                    }
                }
            } else {
                $select->where($condition);
            }
        }
        if (!empty($sortOrder)) {
            $sortCondition = '';
            if (is_array($sortOrder)) {
                foreach ($sortOrder as $sortKey => $sortVal) {
                    $sortCondition[] = ((isset($filterColumns[$sortKey]) && isset($filterColumns[$sortKey]['index'])) ? $filterColumns[$sortKey]['index'] : $sortKey) . ' ' . $sortVal;
                }
            } else {
                $sortCondition = $sortOrder;
            }
            $select->order($sortCondition);
        }
        $modelCollection = Uni_Core_Model_Collection::getInstance($select, $modelClass);
        $modelCollection->setItemCountPerPage($modelCollection->getTotalItemCount());
        return $modelCollection;
    }

    /**
     * Get single row data
     *
     * @param mixed $condition
     * @return type 
     */
    public function getRow($condition=null) {
        return $this->fetchRow($condition);
    }

    /**
     * Processing data before save
     *
     * @param array $data
     * @return array 
     */
    protected function _preSave(array $data) {
        return $data;
    }

    /**
     * Processing data after save
     *
     * @param array $data
     * @return array 
     */
    protected function _postSave(array $data) {
        return $data;
    }

    /**
     * Delete rows from database
     *
     * @param mixed $condition
     * @return int Returns the number of rows deleted 
     */
    public function delete($condition=NULL) {
        $rows = 0;
        $this->_preDelete($condition);
        if (NULL !== $condition) {
            $rows = parent::delete($condition);
        } else if ($this->getId()) {
            $where = $this->getAdapter()->quoteInto($this->getAdapter()->quoteIdentifier($this->primary) . '=?', $this->getId());
            $rows = parent::delete($where);
        }
        $this->_postDelete($condition, $rows);
        return $rows;
    }

    /**
     * Processing data before delete
     *
     * @param mixed $condition 
     */
    protected function _preDelete($condition) {
        
    }

    /**
     * Processing data after delete
     *
     * @param mixed $condition
     * @param int $rows number of rows deleted
     */
    protected function _postDelete($condition, $rows) {
        
    }

    /**
     * Gets primary key field value
     *
     * @return string 
     */
    public function getId() {
        $pkField = self::getPropertyName($this->primary);
        return (array_key_exists($pkField, $this->data) ? $this->data[$pkField] : NULL);
    }

    /**
     * Setting primary key
     *
     * @param string $id 
     */
    public function setId($id) {
        $pkField = self::getPropertyName($this->primary);
        $this->data[$pkField] = $id;
    }

    /**
     * Trapping Call of undefined methods.
     *
     * @param string $method
     * @param array $params
     * @return mixed 
     */
    public function __call($method, $params) {
        $act = substr($method, 0, 3);
        $key = lcfirst(substr($method, 3));
        if ($act == self::GET) {
            return isset($this->data[$key]) ? $this->data[$key] : NULL;
        } else if ($act == self::SET) {
            $this->data[$key] = $params[0];
            return $this;
        }
    }

    /**
     * Makes conventional property name from key
     *
     * @param string $key
     * @return string 
     */
    public static function getPropertyName($key) {
        return Fox::getCamelCase($key);
    }

    /**
     * Clears object data
     * 
     * @return void
     */
    public function unsetData() {
        $this->data = array();
    }

    /**
     * Constructs options for source model field
     *
     * @param array $fields
     * @return array 
     */
    public function _getOptionArray(array $fields) {
        $labelField = NULL;
        $valueField = NULL;
        if (!empty($fields)) {
            if (count($fields) == 2) {
                $labelField = $fields[0];
                $valueField = $fields[1];
            } else {
                $labelField = $fields[0];
            }
        }
        $options = array('' => '');
        $collection = $this->getCollection();
        $i = 0;
        foreach ($collection as $data) {
            if ($i == 0) {
                $labelField = ($labelField && isset($data[$labelField])) ? $labelField : (isset($data[$this->primary]) ? $this->primary : '');
                $valueField = ($valueField && isset($data[$valueField])) ? $valueField : $labelField;
                if (!$labelField || !$valueField) {
                    break;
                }
                $i++;
            }
            $options[$data[$valueField]] = $data[$labelField];
        }
        return $options;
    }

    /**
     * Manages special characters
     *
     * @param array $accArray
     * @return array 
     */
    public function manageSpecialQuotes($accArray) {
        foreach ($accArray as $key => $value) {
            if (is_array($value)) {
                $accArray[$key] = $this->manageSpecialQuotes($value);
            } else {
                $accArray[$key] = trim(stripslashes($value));
            }
        }
        return $accArray;
    }

}