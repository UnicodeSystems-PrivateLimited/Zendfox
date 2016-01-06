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
 * Class Uni_Util_File
 * performs operations on file system
 *  
 * @category    Uni
 * @package     Uni_Util 
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Util_File {
    
    /**
     * Delete directories and files
     * 
     * @param string $directory directory path
     * @param boolean $deleteDirectories does not delete directory if FALSE
     * @return boolean 
     * @throws Exception
     */
    static function deleteDirectoryAndFiles($directory, $deleteDirectories = TRUE) {
        $ds=DIRECTORY_SEPARATOR;
        $dirList = array($directory);
        $dirs=array();
        while (count($dirList) > 0) {
            $dir = array_shift($dirList);
            if (substr($dir, -1) == $ds) {
                $dir = substr($dir, 0, -1);
            }
            if (!file_exists($dir)) {
                throw new Exception('"'.$dir.'" not found');
            }
            if(!is_dir($dir)){
                throw new Exception('"'.$dir.'" is not a directory');
            }
            if (!is_readable($dir)) {
                throw new Exception('"'.$dir.'" is not readable');
            }
            $directoryHandle = opendir($dir);

            while ($contents = readdir($directoryHandle)) {
                if ($contents != '.' && $contents != '..') {
                    $path = $dir . $ds . $contents;
                    if (is_dir($path)) {
                        $dirList[] = $path;
                    } else {
                        unlink($path);
                    }
                }
            }
            $dirs[]=$dir;
            closedir($directoryHandle);
        }
        if ($deleteDirectories) {
            $dirs=array_reverse($dirs);
            for ($i = 0; $i < count($dirs); $i++) {
                if (!rmdir($dirs[$i])) {
                    throw new Exception('Unable to delete "'.$dirs[$i].'"');
                }
            }
        }
        unset($dirList);
        unset($dirs);
        return true;
    }
}