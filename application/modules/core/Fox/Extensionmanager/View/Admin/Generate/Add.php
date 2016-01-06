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
 * Class Fox_Extensionmanager_View_Admin_Generate_Add
 * 
 * @uses Fox_Core_View_Admin_Form_Tabs
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Generate_Add extends Fox_Core_View_Admin_Form_Tabs {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        if(Fox::getModel('extensionmanager/session')->getFormData()){
            $this->editing = true;
            $this->addButton(array("type"=>"button", "button_text"=>"Generate Package" ,
                "url"=> $this->getUrl("*/*/generate-package"),
                "style_class"=> "form-button",
                "confirm"=>true,
                "confirm_text"=> "Unsaved changes to package may lost."));
        }
        $this->deleteButtonUrl = Fox::getUrl("*/*/delete");
        $this->setIsDeleteButtonEnabled(TRUE);
        parent::_prepareContainer();
    }

}