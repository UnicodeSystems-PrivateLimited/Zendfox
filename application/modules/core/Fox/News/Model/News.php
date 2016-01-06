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
 * Class Fox_News_Model_News
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_News
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_News_Model_News extends Uni_Core_Model {
    const STATUS_DISABLED=0;
    const STATUS_ENABLED=1;

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'news_entity';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Get status options
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if date from is greater than date to
     */
    protected function _preSave(array $data) {
        if (!$this->isDateValid($data['date_from'], $data['date_to'])) {
            throw new Exception('"Date From" must be less than "Date To".');
        }
        return $data;
    }

    /**
     * Checks the validity of the date
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @return boolean 
     */
    private function isDateValid($dateFrom, $dateTo) {
        $flag = true;
        if (!empty($dateTo)) {
            if (strtotime($dateFrom) > strtotime($dateTo)) {
                $flag = false;
            }
        }
        return $flag;
    }

}