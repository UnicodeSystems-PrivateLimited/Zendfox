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
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
/**
 * @see Uni_Application_Bootstrap
 */
require_once 'Uni/Application/Bootstrap.php';

/**
 * Class Bootstrap
 * 
 * @uses Uni_Application_Bootstrap
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * 
 */
class Bootstrap extends Uni_Application_Bootstrap {

    /**
     * Load paths
     */
    protected function _initAutoload() {
        Uni_Fox::initPaths(dirname(__FILE__));
    }

    /**
     * Initialize view
     */
    protected function _initView() {
        
    }

    /**
     * Initialize modules
     */
    protected function _initModules() {
        Uni_Fox::initModules($this);
    }

    /**
     * Initialize locale
     */
    protected function _initLocalization() {
        Uni_Fox::initLocale($this);
    }

    /**
     * Initialize session 
     */
    protected function _initSession() {
        Uni_Fox::initSession($this);
    }

}