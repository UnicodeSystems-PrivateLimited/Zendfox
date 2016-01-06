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
 * Class Fox_Extensionmanager_View_Admin_Generate_Add_Providers
 * 
 * @uses Fox_Core_View_Form_View
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Generate_Add_Providers extends Fox_Core_View_Form_View {
    
    /**
     *
     * @var string id of block
     */
    protected $id = '';
    
    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('extensionmanager/generate/providers');
    }
    
    /**
     * Set Block id
     *
     * @param String $id
     */
    public function setId($id){
        $this->id = $id;
    }
    
    /**
     * Get Block id
     *
     * @return String
     */
    public function getId(){
        return $this->id;
    }
}