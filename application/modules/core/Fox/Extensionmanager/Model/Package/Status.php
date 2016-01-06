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
 * Class Fox_Extensionmanager_Model_Package_Status
 * 
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Package_Status {
    const DEFAULT_STATUS = 0;
    const PREPARING = 1;
    const PREPARED = 2;
    const READY = 3;
    const DOWNLOADING = 4;
    const DOWNLOADED = 5;
    const INSTALLING = 6;
    const INSTALLED = 7;
    const UNINSTALLING = 8;
    const UNINSTALLED = 9;
    const ERROR = 99;

    /**
     * Public constructor
     */
    public function __construct() {
        
    }

    /**
     * Retrieve package status options
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray() {
        return array(
            self::DEFAULT_STATUS => 'Default Status',
            self::PREPARING => 'Preparing Package Info',
            self::PREPARED => 'Package Info is Availabele',
            self::READY => 'Package is ready to install',
            self::DOWNLOADING => 'Downloading Package',
            self::DOWNLOADED => 'Package Downloaded',
            self::INSTALLING => 'Installing Package',
            self::INSTALLED => 'Installed Package',
            self::UNINSTALLING => 'Uninstalling Package',
            self::UNINSTALLED => 'Uninstalled Package',
            self::ERROR => 'Installed Failed',
        );
    }

}