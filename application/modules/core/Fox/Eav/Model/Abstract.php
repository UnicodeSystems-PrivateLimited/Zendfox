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
 * Class Fox_Eav_Model_Abstract
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_Model_Abstract extends Uni_Core_Model {
    const EDITABLE_TYPE_FRONTEND=1;
    const EDITABLE_TYPE_BACKEND=2;
    /**
     * @var string
     */
    protected $_eav_field = 'attribute_set_id';
    /**
     * Entity code
     * 
     * @var string
     */
    protected $_eav_entity_type;
    /**
     * Entity prefix
     * 
     * @var string
     */
    protected $_eav_entity_prefix;
    /**
     * Email validation object
     * 
     * @var Zend_Validate_EmailAddress
     */
    protected $emailValidation;
    /**
     * Decimal validation object
     * 
     * @var Zend_Validate_Float
     */
    protected $decimalValidation;
    /**
     * Integer validation object
     * 
     * @var Zend_Validate_Int
     */
    protected $intValidation;
    /**
     * Alphanum validation object
     * 
     * @var Zend_Validate_Alpha 
     */
    protected $letterValidation;
    /**
     * Url validation object
     * 
     * @var Uni_Validate_Url
     */
    protected $urlValidation;
    /**
     * Allowed image extensions
     * 
     * @var array
     */
    protected $validExtensions = array();

    /**
     * Get eav entity prefix
     * 
     * @return string
     */
    public function getEavEntityPrefix() {
        if (NULL == $this->_eav_entity_prefix) {
            $this->_eav_entity_prefix = $this->_name;
        }
        return $this->_eav_entity_prefix;
    }

    /**
     * Get data collection
     * 
     * @param mixed $condition
     * @param mixed $cols
     * @param mixed $sortOrder
     * @param int $limit
     * @param int $pageNumber
     * @param int $type
     * @return Uni_Core_Model_Collection 
     */
    public function getCollection($condition=null, $cols='*', $sortOrder=NULL, $limit=NULL, $pageNumber=NULL, $type=Fox::COLLECTION_MINIMAL) {
        $collection = array();
        $query = $this->getSelectObject($condition, $cols, $sortOrder, $type);
        $collection = Uni_Core_Model_Collection::getInstance($query, NULL);
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
     * Build query
     * 
     * @param mixed $condition
     * @param mixed $cols
     * @param mixed $sortOrder
     * @param int $type
     * @param array|NULL $filterColumns
     * @return Uni_Core_Model_Query 
     */
    private function getSelectObject($condition=NULL, $cols='*', $sortOrder=NULL, $type=Fox::COLLECTION_MINIMAL, $filterColumns=NULL) {
        $mainQuery;
        $metaData = array();
        $selectObjs = array();
        $collectCols = empty($cols);
        $fetchCols = $collectCols ? array() : $cols;
        $sql = "SELECT a.id AS attr_set_id FROM " . Fox::getTableName('eav_attribute_set') . " a JOIN " . Fox::getTableName('eav_entity_type') . " e ON (a.entity_type_id=e.id) WHERE e.entity_type_code='$this->_eav_entity_type'";
        $rows = $this->getAdapter()->fetchAll($sql);
        $eav_entity_prefix = $this->getEavEntityPrefix();
        foreach ($rows as $row) {
            $aidRows = $this->getAdapter()->fetchAll("SELECT at.attribute_code AS attribute_code, at.id AS attribute_id, at.data_type as data_type FROM " . Fox::getTableName('eav_attribute') . " at JOIN " . Fox::getTableName('eav_entity_attribute') . " ea ON (ea.attribute_id=at.id) WHERE ea.attribute_set_id='{$row['attr_set_id']}'");
            foreach ($aidRows as $aidRow) {
                if ($collectCols) {
                    $fetchCols = $aidRow['attribute_code'];
                }
                $attrAlias = 'eav_' . $aidRow['data_type'];
                $metaData[$row['attr_set_id']]['select'][] = '(SELECT `value` FROM ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . '  WHERE ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . ".entity_id=main.id AND " . $eav_entity_prefix . '_' . $aidRow['data_type'] . '.attribute_id=\'' . $aidRow['attribute_id'] . '\') AS ' . $aidRow['attribute_code'];
            }
            if (empty($metaData)) {
                $select = $this->select()->from(array('main' => $this->_name))->where($this->_eav_field . '=?', $row['attr_set_id']);
            } else {
                $select = $this->select()->from(array('main' => $this->_name))->columns($metaData[$row['attr_set_id']]['select'])->where($this->_eav_field . '=?', $row['attr_set_id']);
            }
            $mainQuery = (isset($mainQuery) ? $mainQuery . ' UNION ALL ' : '(') . $select->__toString();
        }
        $mainQuery = $mainQuery . ')';
        $mainSelect = $this->getAdapter()->select()->from(array('subQuery' => '$mainQuery'), $cols);
        if (!empty($condition)) {
            if (is_array($condition)) {
                foreach ($condition as $key => $value) {
                    if (is_array($value) && isset($value['operator'])) {
                        $mainSelect->where($key . $value['operator'] . '?', $value['value']);
                    } else if (isset($filterColumns[$key])) {
                        if (isset($filterColumns[$key]['type'])) {
                            if ($filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_NUMBER) {
                                if (isset($condition[$key]['from']) && $condition[$key]['from']!='') {
                                    $mainSelect->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . ">=?", $condition[$key]['from']);
                                }
                                if (isset($condition[$key]['to']) && $condition[$key]['to']!='') {
                                    $mainSelect->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . "<=?", $condition[$key]['to']);
                                }
                            } else if ($filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_DATE) {
                                if (isset($condition[$key]['from']) && $condition[$key]['from']!='') {
                                    $mainSelect->where((isset($filterColumns[$key]['index']) ? ('date(' . $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) . ')') : ('date(' . $this->getAdapter()->quoteIdentifier($key)) . ')') . ">=?", $condition[$key]['from']);
                                }
                                if (isset($condition[$key]['to']) && $condition[$key]['to']!='') {
                                    $mainSelect->where((isset($filterColumns[$key]['index']) ? ('date(' . $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) . ')') : ('date(' . $this->getAdapter()->quoteIdentifier($key)) . ')') . "<=?", $condition[$key]['to']);
                                }
                            } else if (isset($filterColumns[$key]['type']) && $filterColumns[$key]['type'] == Fox_Core_View_Admin_Table::TYPE_OPTIONS) {
                                $mainSelect->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . "=?", $value);
                            } else {
                                $mainSelect->where((isset($filterColumns[$key]['index']) ? $this->getAdapter()->quoteIdentifier($filterColumns[$key]['index']) : $this->getAdapter()->quoteIdentifier($key)) . " like ?", '%' . $value . '%');
                            }
                        }
                    } else {
                        $mainSelect->where("$key=?", $value);
                    }
                }
            } else {
                $mainSelect->where($condition);
            }
        }
        if (!empty($sortOrder)) {
            $sortCondition = '';
            if (is_array($sortOrder)) {
                foreach ($sortOrder as $sortKey => $sortVal) {
                    $sortCondition[] = $sortKey . ' ' . $sortVal;
                }
            } else {
                $sortCondition = $sortOrder;
            }
            $mainSelect->order($sortCondition);
        }
        $mainQuery = str_replace('`$mainQuery`', $mainQuery, $mainSelect->__toString());
        $selectObjs = new Uni_Core_Model_Query($this->getAdapter(), $mainQuery);
        return $selectObjs;
    }

    /**
     * Get eav form meta data
     * 
     * @param int $attrSetId attribute set id
     * @param int $editable fetch attributes editable from frontend or backend
     * @return array 
     */
    public function getEavFormMetaData($attrSetId, $editable=0) {
        $condition = '';
        if ($editable === self::EDITABLE_TYPE_FRONTEND) {
            $condition = ' AND at.is_editable=' . Fox_Eav_Model_Attribute::ATTRIBUTE_EDITABLE_YES;
        } else if ($editable === self::EDITABLE_TYPE_BACKEND) {
            $condition = ' AND at.is_backend_editable=' . Fox_Eav_Model_Attribute::ATTRIBUTE_EDITABLE_BACKEND_YES;
        } else {
            $condition = ' AND at.is_visible_on_frontend=' . Fox_Eav_Model_Attribute::ATTRIBUTE_VISIBILE_ON_FRONTEND_YES;
        }
        $eavFormMeta = array();
        $metaSql = "SELECT * FROM (SELECT g.group_name as group_name,g.sort_order,at.id AS attribute_id,at.attribute_code AS attribute_code, at.input_type AS input_type, at.frontend_label as frontend_label, at.validation as validation, at.is_required as is_required, at.position as position, at.default_value as default_value, at.source_model as source_model,at.position as attribute_order FROM " . Fox::getTableName('eav_entity_metadata_attribute') . " at JOIN " . Fox::getTableName('eav_entity_metadata_group') . " g ON at.group_id=g.id JOIN " . Fox::getTableName('eav_entity_type') . " t ON g.entity_type_id=t.id WHERE t.entity_type_code='$this->_eav_entity_type'" . $condition
                . ' UNION ALL '
                . "SELECT g.attribute_group_name as group_name,g.sort_order,at.id AS attribute_id,at.attribute_code AS attribute_code, at.input_type AS input_type, at.frontend_label as frontend_label, at.validation as validation, at.is_required as is_required, at.position as position, at.default_value as default_value, at.source_model as source_model,ea.sort_order as attribute_order FROM " . Fox::getTableName('eav_attribute') . " at JOIN " . Fox::getTableName('eav_entity_attribute') . " ea ON ea.attribute_id=at.id JOIN " . Fox::getTableName('eav_attribute_group') . " g ON ea.attribute_group_id=g.id WHERE ea.attribute_set_id='$attrSetId'" . $condition . ') AS sub ORDER BY sub.sort_order ASC, sub.attribute_order ASC';
        $aidRows = $this->getAdapter()->fetchAll($metaSql);
        foreach ($aidRows as $aidRow) {
            switch ($aidRow['input_type']) {
                case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_SELECT:
                case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_RADIO:
                    if (empty($aidRow['source_model'])) {
                        $optData = $this->getMultiOptionsFromEavOptions($aidRow['attribute_id']);
                        $multiOptions = $optData['options'];
                        $default = $optData['default'];
                    } else {
                        $multiOptions = $this->getMultiOptionsFromSourceModel($aidRow['source_model']);
                    }
                    break;
                default :
                    unset($default);
                    $multiOptions = FALSE;
            }
            $eavFormMeta[$aidRow['group_name']][] = array(
                'attribute_code' => $aidRow['attribute_code'],
                'input_type' => $aidRow['input_type'],
                'frontend_label' => $aidRow['frontend_label'],
                'validation' => $aidRow['validation'],
                'is_required' => $aidRow['is_required'],
                'position' => $aidRow['position'],
                'multiOptions' => $multiOptions,
                'default_value' => (isset($default) ? $default : $aidRow['default_value'])
            );
        }
        return $eavFormMeta;
    }

    /**
     * Get attribute multi options data
     * 
     * @param int $attrId attribute id
     * @return array
     */
    function getMultiOptionsFromEavOptions($attrId) {
        $optRows = $this->getAdapter()->fetchAll('SELECT opt.label AS opt_label,opt.value AS opt_value, opt.is_default AS is_default FROM ' . Fox::getTableName('eav_attribute_option') . ' opt WHERE opt.attribute_id=\'' . $attrId . '\' ORDER BY opt.sort_order');
        $multiOptions = array();
        foreach ($optRows as $optRow) {
            $multiOptions[$optRow['opt_value']] = $optRow['opt_label'];
            if ($optRow['is_default']) {
                $default = $optRow['opt_value'];
            }
        }
        return array('options' => $multiOptions, 'default' => isset($default) ? $default : '');
    }

    /**
     * Construct and return multi options from source model
     * 
     * @param string $sourceModel
     * @return array
     */
    function getMultiOptionsFromSourceModel($sourceModel) {
        $modelParts = explode(':', $sourceModel);
        $model = Fox::getModel($modelParts[0]);
        if ($model) {
            $multiOptions = $model->_getOptionArray(((count($modelParts) > 0) ? array_slice($modelParts, 1) : NULL));
        }
        return isset($multiOptions) ? $multiOptions : array();
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if uploads directory not found
     */
    protected function _preSave(array $data) {
        if (!empty($_FILES)) {
            $path = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . $this->_eav_entity_type;
            if (!file_exists($path)) {
                if (!@mkdir($path, 777, TRUE)) {
                    throw new Exception('uploads directory was not found.');
                }
            }
            foreach ($_FILES as $key => $file) {
                if (isset($file['tmp_name']) && $file['tmp_name']) {
                    if (($pos = strrpos($file['name'], '.')) > -1) {
                        $ext = substr($file['name'], ($pos + 1));
                        $fileName = 'FILE-' . time() . '.' . $ext;
                        $filePath = $path . DIRECTORY_SEPARATOR;
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination($filePath);
                        $upload->receive($key);
                        $uploadedfilepath = $upload->getFileName($key);
                        $filterFileRename = new Zend_Filter_File_Rename(array('target' => ($filePath . $fileName), 'overwrite' => true));
                        $filterFileRename->filter($uploadedfilepath);
                        $method = Uni_Core_Model::SET . Fox::getCamelCase($key);
                        $this->$method($fileName);
                    }
                }
            }
        }
        return parent::_preSave($data);
    }

    /**
     * Save eav record
     *
     * @return Uni_Core_Model 
     */
    public function save() {
        $update = FALSE;
        try {
            $fields = $this->_getCols();
            $data = $this->getData($fields);
            $id = NULL;
            $data = $this->_preSave($data);
            $attrSetIdMethod = 'get' . ucfirst(self::getPropertyName($this->_eav_field));
            $attrSetId = $this->$attrSetIdMethod();
            $eav_entity_prefix = $this->getEavEntityPrefix();
            $aidMetaRows = $this->getAdapter()->fetchAll("SELECT at.attribute_code AS attribute_code, at.id AS attribute_id, at.data_type as data_type,at.validation,at.is_editable,at.is_visible_on_frontend,at.is_required,at.frontend_label FROM " . Fox::getTableName('eav_entity_metadata_attribute') . " at JOIN " . Fox::getTableName('eav_entity_type') . " e ON at.entity_type_id=e.id WHERE (at.is_editable=" . self::EDITABLE_TYPE_FRONTEND . " OR at.is_visible_on_frontend=" . Fox_Eav_Model_Attribute::ATTRIBUTE_VISIBILE_ON_FRONTEND_YES . ") AND e.entity_type_code='" . $this->_eav_entity_type . "'");
            foreach ($aidMetaRows as $aidMetaRow) {
                $eavFieldMethod = 'get' . ucfirst(self::getPropertyName($aidMetaRow['attribute_code']));
                $value = $this->$eavFieldMethod();
                if ($aidMetaRow['validation'] && $value) {
                    $valid = $this->validateAttributeValue($aidMetaRow['validation'], $value);
                    if (!$valid['success']) {
                        throw new Exception($valid['errors']);
                    }
                }
            }
            if (is_array($data) && array_key_exists($this->primary, $data)) {
                $id = $data[$this->primary];
            }
            if ($id) {
                $this->update($data, "$this->primary='$id'");
                $update = TRUE;
            } else {
                $this->insert(array_filter($data, 'Uni_Core_Model::removeNull'));
                $id = $this->getAdapter()->lastInsertId($this->_name);
                $data[$this->primary] = $id;
            }
            if (isset($id)) {
                $aidRows = $this->getAdapter()->fetchAll("SELECT at.attribute_code AS attribute_code, at.id AS attribute_id, at.data_type as data_type,at.validation,at.is_editable,at.is_visible_on_frontend,at.is_required,at.is_unique,at.frontend_label FROM " . Fox::getTableName('eav_attribute') . " at JOIN " . Fox::getTableName('eav_entity_attribute') . " ea ON (ea.attribute_id=at.id) WHERE ea.attribute_set_id='$attrSetId'");
                foreach ($aidRows as $aidRow) {
                    $eavFieldMethod = 'get' . ucfirst(self::getPropertyName($aidRow['attribute_code']));
                    $value = $this->$eavFieldMethod();
                    if ($aidRow['is_required'] == Fox_Eav_Model_Attribute::ATTRIBUTE_REQUIRED_YES) {
                        if (!$value) {
                            throw new Exception('"' . $aidRow['frontend_label'] . '" is required.');
                        }
                    }
                    if ($aidRow['is_unique'] == Fox_Eav_Model_Attribute::ATTRIBUTE_UNIQUE_YES) {
                        $querySql = 'SELECT COUNT(entity_id) FROM ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . " WHERE value='$value' AND attribute_id='" . $aidRow['attribute_id'] . "' AND entity_id!='$id'";
                        $duplicateCount = (int) $this->getAdapter()->fetchOne($querySql);
                        if ($duplicateCount > 0) {
                            throw new Exception('"' . $aidRow['frontend_label'] . '" must be unique.');
                        }
                    }
                    if ($aidRow['validation'] && $value) {
                        $valid = $this->validateAttributeValue($aidRow['validation'], $value);
                        if (!$valid['success']) {
                            throw new Exception($valid['errors']);
                        }
                    }
                    $insertSql = '';
                    $insertSql = 'INSERT INTO ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . "(`value`,`attribute_id`,`entity_id`) VALUES(?,?,?) ON DUPLICATE KEY UPDATE value='$value',attribute_id='" . $aidRow['attribute_id'] . "',entity_id='$id'";
                    $stmt = $this->getAdapter()->query($insertSql, array($value, $aidRow['attribute_id'], $id));
                    if (!$stmt) {
                        throw new Exception('Error while saving record for "' . $this->_name . '".');
                    }
                }
            }
            $this->_postSave($data);
            $this->load($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
                $condition.= ( $condition != '' ? " AND $key='$val'" : "$key='$val'");
            }
        } else {
            $condition = ($field ? $field : $this->primary) . '=' . "'$value'";
        }
        $record = $this->fetchRow($condition);
        if ($record) {
            $mainData = $record->toArray();
            $metaData = array();
            $attrSetId = $mainData[$this->_eav_field];
            $eav_entity_prefix = $this->getEavEntityPrefix();
            $aidRows = $this->getAdapter()->fetchAll("SELECT at.attribute_code AS attribute_code, at.id AS attribute_id, at.data_type as data_type FROM " . Fox::getTableName('eav_attribute') . " at JOIN " . Fox::getTableName('eav_entity_attribute') . " ea ON (ea.attribute_id=at.id) WHERE ea.attribute_set_id='$attrSetId'");
            foreach ($aidRows as $aidRow) {
                $attrAlias = 'eav_' . $aidRow['data_type'];
                $metaData['select'][] = '(SELECT `value` FROM ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . '  WHERE ' . $eav_entity_prefix . '_' . $aidRow['data_type'] . ".entity_id=main.id AND " . $eav_entity_prefix . '_' . $aidRow['data_type'] . '.attribute_id=\'' . $aidRow['attribute_id'] . '\') AS ' . $aidRow['attribute_code'];
            }
            if (!empty($metaData)) {
                $select = $this->select()->from(array('main' => $this->_name), $metaData['select'])->where($this->primary . '=?', $mainData[$this->primary]);
                $eavData = $this->fetchRow($select);
                if ($eavData) {
                    $mainData = array_merge($mainData, $eavData->toArray());
                }
            }
            return $this->setData($mainData);
        }
        return NULL;
    }

    /**
     * Get object collection
     *
     * @param mixed $condition
     * @param mixed $cols
     * @param mixed $sortOrder
     * @param array $filterColumns
     * @param int $type
     * @return Uni_Core_Model_Collection 
     */
    public function getModelCollection($condition=NULL, $cols='*', $sortOrder=NULL, $filterColumns=NULL, $type=Fox::COLLECTION_MINIMAL) {
        $modelClass = get_class($this);
        $modelCollection = array();
        $query = $this->getSelectObject($condition, $cols, $sortOrder, $type, $filterColumns);
        $modelCollection = Uni_Core_Model_Collection::getInstance($query, $modelClass);
        $modelCollection->setItemCountPerPage($modelCollection->getTotalItemCount());
        return $modelCollection;
    }

    /**
     * Get eav entity code
     * 
     * @return string
     */
    public function getEavEntityType() {
        return $this->_eav_entity_type;
    }

    /**
     * Validate attributes
     * 
     * @param string $validation attribute validation class name
     * @param string $value attribute value
     * @return array returns success and errors as array keys
     */
    protected function validateAttributeValue($validation, $value) {
        $valid = array('success' => TRUE, 'errors' => '');
        switch ($validation) {
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_DECIMAL:
                if (!$this->decimalValidation) {
                    $this->decimalValidation = new Zend_Validate_Float();
                }
                $valid['success'] = $this->decimalValidation->isValid($value);
                $valid['errors'] = '"' . $value . '" contains invalid digits.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_EMAIL:
                if (!$this->emailValidation) {
                    $this->emailValidation = new Zend_Validate_EmailAddress();
                }
                $valid['success'] = $this->emailValidation->isValid($value);
                $valid['errors'] = '"' . $value . '" is not a valid email address.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_INT:
                if (!$this->intValidation) {
                    $this->intValidation = new Zend_Validate_Int();
                }
                $valid['success'] = $this->intValidation->isValid($value);
                $valid['errors'] = '"' . $value . '" is not a valid integer.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_LETTERS:
                if (!$this->letterValidation) {
                    $this->letterValidation = new Zend_Validate_Alpha(true);
                }
                $valid['success'] = $this->letterValidation->isValid($value);
                $valid['errors'] = '"' . $value . '" contains invalid characters.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_DATE:
                $valid['success'] = strtotime($value) > 0;
                $valid['errors'] = '"' . $value . '" is invalid date.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_URL:
                if (!$this->urlValidation) {
                    $this->urlValidation = new Uni_Validate_Url();
                }
                $valid['success'] = $this->urlValidation->isValid($value);
                $valid['errors'] = '"' . $value . '" is not a valid url.';
                break;
            case Fox_Eav_Model_Attribute::ATTRIBUTE_VALIDATION_IMAGE:
                if (empty($this->validExtensions)) {
                    $this->validExtensions = array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'tiff');
                }
                $extPos = strrpos($value, '.');
                if (!$extPos || (!empty($this->validExtensions) && !in_array(substr($value, $extPos + 1), $this->validExtensions))) {
                    $valid['success'] = FALSE;
                    $valid['errors'] = 'Invalid image was given.';
                }
                break;
            default:
                break;
        }
        return $valid;
    }

}