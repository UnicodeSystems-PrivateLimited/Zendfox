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
 * Class Fox_Extensionmanager_Model_Generate_DirectoryToXml
 * 
 * @uses Fox_Extensionmanager_Model_Generate_ConvertArrayToXml
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Generate_DirectoryToXml extends Fox_Extensionmanager_Model_Generate_ConvertArrayToXml {

    /**
     * Contains Directory array
     * 
     * @var array 
     */
    public $arr;

    /**
     * Contains Directory path
     * 
     * @var array 
     */
    private $path;

    /**
     * Public Constructor
     * @param $path
     */
    public function __construct($path) {
        $this->arr = array();
        $this->path = $path;
    }

    /**
     * Traverses Files and Directories inside given path Recursively
     * @param $path to traverse
     */
    public function traverseDirFiles($path) {
        $dir = opendir($path);
        $files = array();
        $f = array();
        $skip = array(Fox::getDirectoryPath() . DS . "var",
            Fox::getDirectoryPath() . DS . "library" . DS . "Zend",
            Fox::getExtensionDirectoryPath(),
            Fox::getDirectoryPath() . DS . "theme" . DS . "installer",
            APPLICATION_PATH . DS . "views" . DS . "installer"
        );
        while ($ft = readdir($dir)) {
            if (in_array($ft, array(".", "..")) || in_array($path . DS . $ft, $skip))
                continue;
            $f[] = $ft;
        }
        sort($f);
        foreach ($f as $file) {
            if (filetype($path . DS . $file) != "dir") {
                $files[] = $file;
            } else {
                $this->traverseDirFiles($path . DS . $file);
            }
        }
        foreach ($files as $f):
            $this->processFiles($path, $f);
        endforeach;
        closedir($dir);
    }

    /**
     * Put $file to path array
     * 
     * @param $path, $file
     */
    protected function processFiles($path, $file) {
        if (trim($path) == trim($this->path)) {
            $arr_to_form = 'this->arr[root]';
        } else {
            $dat = '';
            $arr = explode($this->path . DS, $path);
            $arr = explode(DS, $arr[1]);
            foreach ($arr as $i => $v) {
                $dat .= "[$v]";
            }
            $arr_to_form = 'this->arr[root]' . $dat;
        }
        $searches = array("[", "]");
        $reps = array("['", "']");
        $arr_2 = str_replace($searches, $reps, trim($arr_to_form));
        $vr = $arr_2 . '[]';
        eval("\$$vr=\"$file\";");
    }

}