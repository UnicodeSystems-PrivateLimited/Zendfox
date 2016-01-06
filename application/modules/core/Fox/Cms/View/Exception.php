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
 * Class Fox_Cms_View_Exception
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_View_Exception extends Fox_Core_View_Template {

    /**
     *
     * @var string
     */
    protected $fileName;
    /**
     *
     * @var string
     */
    protected $filePath;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('cms/exception');
    }

    /**
     * Set error file information
     * 
     * @param string $fName
     * @param string $fPath 
     */
    function setErrorFile($fName, $fPath) {
        $this->fileName = $fName;
        $this->filePath = $fPath;
    }

    /**
     * Retrieve exception printing status
     * 
     * @return int Returns 1 if exception printing is enabled
     */
    function isExceptionPrintingEnabled() {
        return Fox::getPreference('core/debug/exception_printing');
    }

    /**
     * Get file name and path of error file
     * 
     * @return array
     */
    function getErrorFile() {
        return array('fileName' => $this->fileName, 'filePath' => $this->filePath);
    }

}