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
 * Class Uni_Core_Model_Collection
 * extended version of Zend_Paginator
 * 
 * @uses        Zend_Paginator
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Model_Collection extends Zend_Paginator {

    /**
     *
     * @var string 
     */
    private $modelClass;
    
    /**
     *
     * @var mixed 
     */
    private $dbTableSelect;
    
    /**
     *
     * @var int 
     */
    protected $itemCountStart;
    
    /**
     *
     * @var int 
     */
    protected $itemCountEnd;

    /**
     *
     * @param mixed $dbTableSelect
     * @param string $modelClass
     * @return Uni_Core_Model_Collection 
     */
    static function getInstance($dbTableSelect, $modelClass=NULL) {        
        $collection=self::factory($dbTableSelect);
        $collection->setModelClass($modelClass);
        $collection->setDbTableSelect($dbTableSelect);
        return $collection;
    }

    /**
     * Factory.
     *
     * @param  mixed $data
     * @param  string $adapter
     * @param  array $prefixPaths
     * @return Uni_Core_Model_Collection
     */
    public static function factory($data, $adapter = self::INTERNAL_ADAPTER, array $prefixPaths = null) {
        if ($data instanceof Zend_Paginator_AdapterAggregate) {
            return new self($data->getPaginatorAdapter());
        } else {
            if ($adapter == self::INTERNAL_ADAPTER) {
                if (is_array($data)) {
                    $adapter = 'Array';
                } else if ($data instanceof Zend_Db_Table_Select) {
                    $adapter = 'DbTableSelect';
                } else if ($data instanceof Zend_Db_Select) {
                    $adapter = 'DbSelect';
                } else if ($data instanceof Iterator) {
                    $adapter = 'Iterator';
                } else if (is_integer($data)) {
                    $adapter = 'Null';
                } else if ($data instanceof Uni_Core_Model_Query) {
                    return new self(new Uni_Core_Paginator_Adapter_Query($data));
                } else {
                    $type = (is_object($data)) ? get_class($data) : gettype($data);

                    /**
                     * @see Zend_Paginator_Exception
                     */
                    require_once 'Zend/Paginator/Exception.php';

                    throw new Zend_Paginator_Exception('No adapter for type ' . $type);
                }
            }
            $pluginLoader = self::getAdapterLoader();

            if (null !== $prefixPaths) {
                foreach ($prefixPaths as $prefix => $path) {
                    $pluginLoader->addPrefixPath($prefix, $path);
                }
            }

            $adapterClassName = $pluginLoader->load($adapter);
            return new self(new $adapterClassName($data));
        }
    }
    
    /**
     *
     * @param string $modelClass 
     */
    public function setModelClass($modelClass){
        $this->modelClass=$modelClass;
    }
    
    /**
     *
     * @param mixed $dbTableSelect 
     */
    public function setDbTableSelect($dbTableSelect) {
        $this->dbTableSelect = $dbTableSelect;
    }
    
    /**
     * Returns the items for a given page.
     *
     * @return Traversable
     */
    public function getItemsByPage($pageNumber) {
        $pageNumber = $this->normalizePageNumber($pageNumber);

        if ($this->_cacheEnabled()) {
            $data = self::$_cache->load($this->_getCacheId($pageNumber));
            if ($data !== false) {
                return $data;
            }
        }

        $offset = ($pageNumber - 1) * $this->getItemCountPerPage();

        $items = $this->_adapter->getItems($offset, $this->getItemCountPerPage());

        $filter = $this->getFilter();

        if ($filter !== null) {
            $items = $filter->filter($items);
        }
        if (isset($this->modelClass)) {
            $modelsArray = array();
            for ($i = 0; $i < count($items); $i++) {
                $modelObj = new $this->modelClass();
                foreach ($items[$i] as $k => $v) {
                    $words = explode('_', $k);
                    $mName = '';
                    foreach ($words as $word) {
                        $mName = $mName . ucfirst($word);
                    }
                    $method = Uni_Core_Model::SET . ucfirst($mName);
                    $modelObj->$method($v);
                }
                $modelsArray[] = $modelObj;
            }
        }
        if (isset($modelsArray)) {
            $items = $modelsArray;
            unset($modelsArray);
        }

        if (!$items instanceof Traversable) {
            $items = new ArrayIterator($items);
        }

        if ($this->_cacheEnabled()) {
            self::$_cache->save($items, $this->_getCacheId($pageNumber), array($this->_getCacheInternalId()));
        }

        return $items;
    }

    /**
     * Returns an item from a page.  The current page is used if there's no
     * page sepcified.
     *
     * @param  integer $itemNumber Item number (1 to itemCountPerPage)
     * @param  integer $pageNumber
     * @return mixed
     */
    public function getItem($itemNumber, $pageNumber = null) {
        if ($pageNumber == null) {
            $pageNumber = $this->getCurrentPageNumber();
        } else if ($pageNumber < 0) {
            $pageNumber = ($this->count() + 1) + $pageNumber;
        }

        $page = $this->getItemsByPage($pageNumber);
        $itemCount = $this->getItemCount($page);

        if ($itemCount == 0) {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';

            throw new Zend_Paginator_Exception('Page ' . $pageNumber . ' does not exist');
        }

        if ($itemNumber < 0) {
            $itemNumber = ($itemCount + 1) + $itemNumber;
        }

        $itemNumber = $this->normalizeItemNumber($itemNumber);

        if ($itemNumber > $itemCount) {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';

            throw new Zend_Paginator_Exception('Page ' . $pageNumber . ' does not'
                    . ' contain item number ' . $itemNumber);
        }

        if ($this->modelClass) {
            $modelObj = new $this->modelClass();
            $row = $page[$itemNumber - 1];
            foreach ($row as $k => $v) {
                $words = explode('_', $k);
                $mName = '';
                foreach ($words as $word) {
                    $mName = $mName . ucfirst($word);
                }
                $method = Uni_Core_Model::SET . ucfirst($mName);
                $modelObj->$method($v);
            }
            return $modelObj;
        }
        return $page[$itemNumber - 1];
    }

    /**
     * Serial no of starting record
     *
     * @return int 
     */
    public function getItemCountEnd() {
        $itemExpect = intval($this->getItemCountPerPage()) * intval($this->getCurrentPageNumber());
        if (NULL == $this->itemCountEnd) {
            $this->itemCountEnd = ($itemExpect > $this->getTotalItemCount() ? $this->getTotalItemCount() : $itemExpect);
        }
        return $this->itemCountEnd;
    }

    /**
     * Serial no of last record
     *
     * @return int 
     */
    public function getItemCountStart() {
        if (NULL == $this->itemCountStart) {
            $this->itemCountStart = (intval($this->getItemCountPerPage()) * ($this->getCurrentPageNumber() - 1) + 1);
        }
        return $this->itemCountStart;
    }

    /**
     *
     * @return mixed 
     */
    public function getSelect(){
        return ($this->dbTableSelect instanceof Zend_Db_Select)?$this->dbTableSelect:FALSE;
    }
    
}