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
 */
/**
 * Zendfox
 *  
 * @category  Fox
 * @package   Fox 
 * @author Unicode Systems Zendfox Core Team <zendfox@unicodesystems.in>
 */

/**
 * Error reporting
 */
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_STRICT & ~E_WARNING);
/** 
 * Define path to application directory
 */
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

/**
 * Define application environment
 */
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
/**
 * Ensure library is on include_path
 */
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

/**
 * @see Uni_Fox
 */
require_once 'Uni/Fox.php';
/**
 *  Run Fox Framework
 */
Uni_Fox::runApp();