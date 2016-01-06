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
 * Class Fox_Extensionmanager_View_Admin_Dependency
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Dependency extends Uni_Core_Admin_View {

    /**
     * Contains messages
     * 
     * @var array
     */
    protected $messages = array();

    /**
     * Check is compatible package
     * 
     * @return boolean
     */
    private function isCompatible() {
        $packageInfo = $this->getViewOptions('package');
        if (isset($packageInfo['compatibility']) && $packageInfo['compatibility']) {
            $compatibility = explode(",", $packageInfo['compatibility']);
            if (in_array(Fox::getVersion(), $compatibility)) {
                return true;
            } else {
                $this->messages[] = "Extension is not compatible with version :" . Fox::getVersion();
            }
        } else {
            $this->messages[] = "Unknown compatibility";
        }
        return false;
    }

    /**
     * Check stability is reliable
     * 
     * @return boolean
     */
    private function isReliableStability() {
        $packageInfo = $this->getViewOptions('package');
        if (isset($packageInfo['stability']) && $packageInfo['stability']) {
            if ($packageInfo['stability'] >= Fox::getPreference('extensionmanager/downloader/preferred_state')) {
                return true;
            } else {
                $this->messages[] = "This extension is under " . Fox::getModel('extensionmanager/package/state')->getOptionText($packageInfo['stability']) . " state";
            }
        } else {
            $this->messages[] = "Unknown stability";
        }
        return false;
    }

    /**
     * Get package information
     * 
     * @return array
     */
    public function getInfo() {
        return $this->getViewOptions('package');
    }

    /**
     * Get messages
     * 
     * @return array
     */
    public function getMessages() {
        $this->isCompatible();
        $this->isReliableStability();
        return $this->messages;
    }
    
//    private function checkForInstalled(){
//        $packages = Fox::getView("extensionmanager/admin/packages");
//    }

}