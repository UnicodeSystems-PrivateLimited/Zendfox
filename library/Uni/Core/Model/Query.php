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
 * Class Uni_Core_Model_Query
 *   
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 *
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License

 */
class Uni_Core_Model_Query {

    /**
     *
     * @var Zend_Db_Adapter_Pdo_Mysql 
     */
    protected $adapter;
    
    /**
     *
     * @var string 
     */
    protected $query;

    /**
     * Constructs new object of Uni_Core_Model_Query
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $adapter
     * @param string $query 
     */
    function __construct(Zend_Db_Adapter_Pdo_Mysql $adapter, $query=NULL) {
        $this->adapter = $adapter;
        $this->query = $query;
    }

    /**
     *
     * @return Zend_Db_Adapter_Pdo_Mysql 
     */
    public function getAdapter() {
        return $this->adapter;
    }

    /**
     *
     * @param Zend_Db_Adapter_Pdo_Mysql $adapter 
     */
    public function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    /**
     *
     * @return string 
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     *
     * @param string $query 
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * Fetches elements according to limit
     *
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array 
     */
    public function fetchAll($offset, $itemCountPerPage) {
        return $this->adapter->fetchAll($this->query . ' LIMIT ' . $itemCountPerPage . ' OFFSET ' . $offset);
    }

    /**
     * Gets record count for current page
     *
     * @return int 
     */
    public function getCount() {
        $count = 0;
        $count = $this->adapter->fetchOne('SELECT COUNT(1) as fox_query_count FROM (' . $this->query . ') AS fox_subquery_count');
        return $count;
    }

}