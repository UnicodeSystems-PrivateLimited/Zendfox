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
 * Class Fox_Member_View_Admin_Member_Edit_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Admin_Member_Edit_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('member/member');
        $this->setForm(new Uni_Core_Form_Eav());
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form_Eav $form 
     */
    protected function _prepareForm(Uni_Core_Form_Eav $form) {
        $form->setName('member')
                ->setMethod('post');
        $model = Fox::getModel($this->getModelKey());
        $id = $this->getRequest()->getParam('id', FALSE);
        $edit = 0;
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $edit = Fox_Eav_Model_Abstract::EDITABLE_TYPE_BACKEND;
            }
        }
        $eavMetaData = $model->getEavFormMetaData(1, $edit);
        $form->createEavForm($eavMetaData);
        parent::_prepareForm($form);
    }

}