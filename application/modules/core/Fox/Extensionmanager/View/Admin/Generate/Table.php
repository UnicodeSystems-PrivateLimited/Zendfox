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
 * Class Fox_Extensionmanager_View_Admin_Generate_Table
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Generate_Table extends Uni_Core_Admin_View {
    
    /**
     * Get all generated packages
     * 
     * @return array
     */
    public function getAllGeneratedPackages(){
        $package = Fox::getModel("extensionmanager/generate/package");
        $pkgPath = $package->getGeneratedPkgDir();
        $pkgList = array();
        $noMod = array('.', '..');
        $doc = new DOMDocument();
        if (is_dir($pkgPath)) {
            $dp = opendir($pkgPath);
            while (($file = readdir($dp))) {
                if (!in_array($file, $noMod)) {
                    $doc->load($pkgPath . DS . $file);
                    if ($doc->documentElement->nodeName == Fox_Extensionmanager_Model_Generate_Package::PACKAGE_ROOT_ELEMENT && $doc->documentElement->childNodes) {
                        $data = array();
                        $nList = $doc->documentElement->childNodes;
                        foreach ($nList as $n) {
                            if (in_array($n->nodeName, array(Fox_Extensionmanager_Model_Generate_Package::PACKAGE_NAME_ELEMENT, Fox_Extensionmanager_Model_Generate_Package::PACKAGE_VERSION_ELEMENT, Fox_Extensionmanager_Model_Generate_Package::PACKAGE_STABILITY_ELEMENT))) {
                                $data[$n->nodeName] = $n->nodeValue;
                            }
                        }
                        if($data){ $pkgList[] = $data; }
                    }
                }
            }
        }
        sort($pkgList);
        return $pkgList;
    }
    
}