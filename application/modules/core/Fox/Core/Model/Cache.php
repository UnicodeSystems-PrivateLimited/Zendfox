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
 * Class Fox_Core_Model_Cache
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Cache extends Uni_Core_Model {
    const STATUS_DISABLED=0;
    const STATUS_ENABLED=1;
    const STATUS_CLEAR=2;
    const CACHE_CODE_PREFERENCE='preference';
    const CACHE_CODE_LAYOUT='layout';

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'core_cache';
        /**
         * Table primary key field
         */
        $this->primary = 'cache_code';
        parent::__construct();
    }

    /**
     * Get all status
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_CLEAR => 'Clear', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

    /**
     * Get all active status
     * 
     * @return array
     */
    public function getActiveStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }

}