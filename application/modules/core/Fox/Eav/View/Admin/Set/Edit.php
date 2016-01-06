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
 * Class Fox_Eav_View_Admin_Set_Edit
 * 
 * @uses Fox_Core_View_Admin_Form_Container
 * @category    Fox
 * @package     Fox_Eav
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Eav_View_Admin_Set_Edit extends Fox_Core_View_Admin_Form_Container {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('eav/set/edit');
    }

    /**
     * Get whether the form is in edit mode
     * 
     * @return boolean
     */
    public function isEditing() {
        return $this->getRequest()->getParam('id', FALSE);
    }

    /**
     * Get delete button action url
     * 
     * @return string
     */
    public function getDeleteButtonUrl() {
        $url = '';
        $id = $this->getRequest()->getParam('id', FALSE);
        if ($id) {
            $url = Fox::getUrl('*/*/delete', array('id' => $id));
        }
        return $url;
    }

    /**
     * Get tree view object
     * 
     * @return Fox_Eav_View_Admin_Set_Edit_Tree
     */
    public function getTree() {
        if (($tree = $this->getChild(self::FORM_KEY))) {
            return $tree;
        } else {
            $formClass = get_class($this) . '_Tree';
            $formView = Fox::getView($formClass);
            if ($formView) {
                $this->addChild(self::FORM_KEY, $formView);
                return $formView;
            }
        }
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        $this->getTree();
    }

}