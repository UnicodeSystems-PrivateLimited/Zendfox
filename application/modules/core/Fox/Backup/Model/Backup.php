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
 * Class Fox_Backup_Model_Backup
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Backup
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Backup_Model_Backup extends Uni_Core_Model {
    const BACKUP_EXTENSION = 'gz';
    const FILE_EXTENSION = 'sql';
    const COMPRESS_RATE = 9;
    const BACKUP_PATH='backups';
    /**
     * @var array
     */
    protected $_foreignKeys = array();
    /**
     * Gz file pointer
     *
     * @var resource
     */
    protected $_handler = null;
    /**
     * @var string
     */
    protected $_filePath = '';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'backup_report';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get backup download path
     * 
     * @return array
     */
    public function getBackupAction() {
        return array('download_path' => Fox::getUrl('*/*/download', array('id' => $this->getId())), 'delete_path' => Fox::getUrl('*/*/delete', array('id' => $this->getId())));
    }

    /**
     * Open backup file
     *
     * @throws Exception
     */
    public function openStream() {
        if (is_null($this->getFileName())) {
            throw new Exception('Backup file path was not specified.');
        }
        $path = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . self::BACKUP_PATH;
        if (!file_exists($path)) {
            if (!@mkdir($path, 0777, true)) {
                throw new Exception('backups folder does not exist in uploads directory.');
            }
            @chmod($path, 0777);
        }
        $this->_filePath = $path . DIRECTORY_SEPARATOR . $this->getFileName();
        $mode = 'wb' . self::COMPRESS_RATE;

        try {
            $this->_handler = gzopen($this->_filePath, $mode);
        } catch (Exception $e) {
            throw new Exception('Backup file "' . $this->getFileName() . '" cannot be read from or written to.');
        }
    }

    /**
     * Create database backup file
     */
    public function createBackup() {
        $this->getAdapter()->beginTransaction();
        try {
            $this->setFileName('DB_' . time() . '.' . self::FILE_EXTENSION . '.' . self::BACKUP_EXTENSION);
            $this->openStream();
            $this->write($this->getHeader());
            $tables = $this->getTables();
            $limit = 1;
            foreach ($tables as $table) {
                $this->write($this->getTableHeader($table) . $this->getTableDropSql($table) . "\n");
                $this->write($this->getTableCreateSql($table, false) . "\n");
                $this->write($this->getTableDataBeforeSql($table));
                $totalRows = (int) $this->getAdapter()->fetchOne('SELECT COUNT(*) FROM ' . $this->getAdapter()->quoteIdentifier($table));
                for ($i = 0; $i < $totalRows; $i++) {
                    $this->write($this->getTableDataSql($table, $limit, $i * $limit));
                }

                $this->write($this->getTableDataAfterSql($table));
            }
            $this->write($this->getTableForeignKeysSql());
            $this->write($this->getFooter());
            $this->closeStream();
            $this->setFileSize($this->getBackupSize());
            $this->setBackupDate(Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s'));
            $this->save();
            $this->getAdapter()->commit();
            Fox::getHelper('core/message')->setInfo('Backup was successfully created.');
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
            $this->getAdapter()->rollback();
            $this->closeStream();
        }
    }

    /**
     * Get database tables
     * 
     * @return array Return table names
     */
    public function getTables() {
        return $this->getAdapter()->fetchCol("Show Tables");
    }

    /**
     * Get drop SQL query for table
     * 
     * @param string $tableName table name
     * @return string
     */
    public function getTableDropSql($tableName) {
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        return 'DROP TABLE IF EXISTS ' . $quotedTableName . ';';
    }

    /**
     * Get create SQL query for table
     * 
     * @param string $tableName table name
     * @return mixed FALSE if table or create query not found
     */
    public function getTableCreateSql($tableName) {
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        $sql = 'SHOW CREATE TABLE ' . $quotedTableName;
        $row = $this->getAdapter()->fetchRow($sql);

        if (!$row || !isset($row['Table']) || !isset($row['Create Table'])) {
            return false;
        }

        $regExp = '/,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
                . 'REFERENCES `([^`]*)` \(`([^`]*)`\)'
                . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
                . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?/';
        $matches = array();
        preg_match_all($regExp, $row['Create Table'], $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $this->_foreignKeys[$tableName][] = sprintf('ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)%s%s', $this->getAdapter()->quoteIdentifier($match[1]), $this->getAdapter()->quoteIdentifier($match[2]), $this->getAdapter()->quoteIdentifier($match[3]), $this->getAdapter()->quoteIdentifier($match[4]), isset($match[5]) ? $match[5] : '', isset($match[7]) ? $match[7] : ''
            );
        }
        return preg_replace($regExp, '', $row['Create Table']) . ';';
    }

    /**
     * Get foreign keys for table(s)
     * 
     * @param string|null $tableName table name
     * @return mixed FALSE if foreign keys does not exists
     */
    public function getTableForeignKeysSql($tableName = null) {
        if (is_null($tableName)) {
            $sql = '';
            foreach ($this->_foreignKeys as $table => $foreignKeys) {
                $sql .= sprintf("ALTER TABLE %s\n  %s;\n", $this->getAdapter()->quoteIdentifier($table), join(",\n  ", $foreignKeys)
                );
            }
            return $sql;
        }
        if (isset($this->_foreignKeys[$tableName]) && ($foreignKeys = $this->_foreignKeys[$tableName])) {
            
        }
        return false;
    }

    /**
     * Get SQL insert query
     * 
     * @param string $tableName table name
     * @param int $count
     * @param int $offset
     * @return string
     */
    public function getTableDataSql($tableName, $count, $offset = 0) {
        $sql = null;
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        $select = $this->getAdapter()->select()
                ->from($tableName)
                ->limit($count, $offset);
        $query = $this->getAdapter()->query($select);

        while ($row = $query->fetch()) {
            if (is_null($sql)) {
                $sql = 'INSERT INTO ' . $quotedTableName . ' VALUES ';
            } else {
                $sql .= ',';
            }

            $sql .= $this->_quoteRow($tableName, $row);
        }

        if (!is_null($sql)) {
            $sql .= ';' . "\n";
        }

        return $sql;
    }

    /**
     * Quote table row
     * 
     * @param string $tableName table name
     * @param array $row
     * @return string
     */
    protected function _quoteRow($tableName, array $row) {
        $describe = $this->getAdapter()->describeTable($tableName);
        $rowData = array();
        foreach ($row as $k => $v) {
            if (is_null($v)) {
                $value = 'NULL';
            } elseif (in_array(strtolower($describe[$k]['DATA_TYPE']), array('bigint', 'mediumint', 'smallint', 'tinyint'))) {
                $value = $v;
            } else {
                $value = $this->getAdapter()->quoteInto('?', $v);
            }
            $rowData[] = $value;
        }
        return '(' . join(',', $rowData) . ')';
    }

    /**
     * Get SQL script for create table
     * 
     * @param string $tableName
     * @param boolean $addDropIfExists
     * @return string 
     */
    public function getTableCreateScript($tableName, $addDropIfExists=false) {
        $script = '';
        if ($this->getAdapter()) {
            $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);

            if ($addDropIfExists) {
                $script .= 'DROP TABLE IF EXISTS ' . $quotedTableName . ";\n";
            }
            $sql = 'SHOW CREATE TABLE ' . $quotedTableName;
            $data = $this->getAdapter()->fetchRow($sql);
            $script.= isset($data['Create Table']) ? $data['Create Table'] . ";\n" : '';
        }

        return $script;
    }

    /**
     * Get table header comment
     *
     * @param string $tableName table name
     * @return string
     */
    public function getTableHeader($tableName) {
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        return "\n--\n"
        . "-- Table structure for table {$quotedTableName}\n"
        . "--\n\n";
    }

    /**
     *
     * @param string $tableName table name
     * @param int $step
     * @return string 
     */
    public function getTableDataDump($tableName, $step=100) {
        $sql = '';
        if ($this->getAdapter()) {
            $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
            $colunms = $this->getAdapter()->fetchRow('SELECT * FROM ' . $quotedTableName . ' LIMIT 1');
            if ($colunms) {
                $arrSql = array();

                $colunms = array_keys($colunms);
                $quote = $this->getAdapter()->getQuoteIdentifierSymbol();
                $sql = 'INSERT INTO ' . $quotedTableName . ' (' . $quote . implode($quote . ', ' . $quote, $colunms) . $quote . ')';
                $sql.= ' VALUES ';

                $startRow = 0;
                $select = $this->getAdapter()->select();
                $select->from($tableName)
                        ->limit($step, $startRow);
                while ($data = $this->getAdapter()->fetchAll($select)) {
                    $dataSql = array();
                    foreach ($data as $row) {
                        $dataSql[] = $this->getAdapter()->quoteInto('(?)', $row);
                    }
                    $arrSql[] = $sql . implode(', ', $dataSql) . ';';
                    $startRow += $step;
                    $select->limit($step, $startRow);
                }

                $sql = implode("\n", $arrSql) . "\n";
            }
        }

        return $sql;
    }

    /**
     * Get SQL header data
     * 
     * @return string
     */
    public function getHeader() {
        $header = "-- Zendfox DB backup\n"
                . "--\n"
                . "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n"
                . "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n"
                . "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n"
                . "/*!40101 SET NAMES utf8 */;\n"
                . "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"
                . "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"
                . "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"
                . "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n";

        return $header;
    }

    /**
     * Get SQL footer data
     * 
     * @return string 
     */
    public function getFooter() {
        $footer = "\n/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n"
                . "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */; \n"
                . "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n"
                . "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n"
                . "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n"
                . "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n"
                . "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n"
                . "\n-- Dump completed on " . gmdate("DD-MM-YYYY") . " GMT";

        return $footer;
    }

    /**
     * Retrieve before insert data SQL fragment
     * 
     * @param string $tableName table name
     * @return string 
     */
    public function getTableDataBeforeSql($tableName) {
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        return "\n--\n"
        . "-- Dumping data for table {$quotedTableName}\n"
        . "--\n\n"
        . "LOCK TABLES {$quotedTableName} WRITE;\n"
        . "/*!40000 ALTER TABLE {$quotedTableName} DISABLE KEYS */;\n";
    }

    /**
     * Retrieve after insert data SQL fragment
     * 
     * @param string $tableName table name
     * @return string
     */
    public function getTableDataAfterSql($tableName) {
        $quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);
        return "/*!40000 ALTER TABLE {$quotedTableName} ENABLE KEYS */;\n"
        . "UNLOCK TABLES;\n";
    }

    /**
     * Write to backup file
     * 
     * @param string $data
     * @return Fox_Backup_Model_Backup
     * @throws Exception
     */
    public function write($data) {
        if (is_null($this->_handler)) {
            throw new Exception('Backup file handler was unspecified.');
        }

        try {
            gzwrite($this->_handler, $data);
        } catch (Exception $e) {
            throw new Exception('An error occurred while writing to the backup file "' . $this->getFileName() . '".');
        }

        return $this;
    }

    /**
     * Close open backup file
     * 
     * @return Fox_Backup_Model_Backup 
     */
    public function closeStream() {
        @gzclose($this->_handler);
        $this->_handler = null;
        return $this;
    }

    /**
     * Get size of backup file in bytes
     * 
     * @return int 
     */
    public function getBackupSize() {
        if (file_exists($this->_filePath) && is_file($this->_filePath)) {
            return filesize($this->_filePath);
        } else {
            return 0;
        }
    }

}