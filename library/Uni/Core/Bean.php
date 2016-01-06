<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

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
 * Class Uni_Core_Bean
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Bean {

    /**
     * Holds all active modules info
     *
     * @var array 
     */
    protected static $moduleInfo = array();

    /**
     *
     * @param array $paths
     * @param string $class
     * @param string $infix
     * @param int $pos
     * @return mixed Object of requested class or FALSE if not found
     */    
    public static function instantiate($paths, $class, $infix, $pos) {
        if (strpos($class, '/') === FALSE) {            
            $fqcn = $class;
            $cpath = str_replace('_', DS, $class);
        } else {
            $classParts = explode('/', $class);
            $module=ucfirst($classParts[0]);
            if (!isset(self::$moduleInfo[$module])) {
                $modules = Uni_Fox::getModules();
                self::$moduleInfo[$module]['namespace'] = $modules[$module]['namespace'];
            }
            $fqcn = self::$moduleInfo[$module]['namespace'];
            $cpath = self::$moduleInfo[$module]['namespace'];
            for ($i = 0; $i < count($classParts); $i++) {
                if ($i == $pos) {
                    $fqcn = $fqcn . '_' . ucfirst($infix);
                    $cpath = $cpath .  DS . ucfirst($infix);
                }
                $fqcn = $fqcn . '_' . ucfirst($classParts[$i]);
                $cpath = $cpath . DS . ucfirst($classParts[$i]);
            }
        }
        while (count($paths)) {
            $path = array_pop($paths);
            $fcpath = $path . DS . $cpath . '.php';
            if (file_exists($fcpath)) {
                require_once $fcpath;
                if (class_exists($fqcn)) {
                    return new $fqcn();
                }
            } else {
            }
        }
        return FALSE;
    }

}