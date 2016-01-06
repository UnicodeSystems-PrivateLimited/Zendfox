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
 * Class Uni_Core_Setup_Sql
 * sql setup class for module installation and upgrade
 *  
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Setup_Sql {

    /**
     * Setup file prefix
     * 
     * @var string
     */
    const SETUP_PRFIX = 'sql_setup';
    
    /**
     * Setup file separator between file and version
     * 
     * @var string
     */
    const SETUP_SEPARATOR = '-';

    /**
     * Database adapter
     * 
     * @var Zend_Db_Adapter
     */
    protected $dbAdapter;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct() {
        $this->dbAdapter = Uni_Fox::getDatabaseAdapter();
    }

    /**
     * Install module sql setup
     * 
     * @param string $dir
     * @param string $sqlVersion
     */
    public function install($dir,$sqlVersion) {
        $this->loadSql($dir.DS.self::SETUP_PRFIX.self::SETUP_SEPARATOR.$sqlVersion.'.php');
    }

    /**
     * Runs the sql query
     * 
     * @param string $sqlQuery
     */
    public function runSetup($sqlQuery) {
        $this->dbAdapter->query($sqlQuery);
    }
    
    /**
     * Runs the sql query to insert record
     * 
     * @param string $tableName
     * @param array $data
     */
    public function insertRecord($tableName,array $data) {
        $this->dbAdapter->insert($tableName, $data);
    }

    /**
     * Includes the sql file
     * 
     * @param string $sqlFile
     */
    private function loadSql($sqlFile) {
        if (file_exists($sqlFile)) {
            include_once $sqlFile;
        }
    }

}