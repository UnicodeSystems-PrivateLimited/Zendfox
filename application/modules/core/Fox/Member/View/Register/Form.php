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
 * Class Fox_Member_View_Register_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Register_Form extends Fox_Core_View_Admin_Form {

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
        $eavMetaData = $model->getEavFormMetaData(1);
        $form->createEavForm($eavMetaData);
        $form->addPrefixPath('Uni_Core_Admin_View_Form_Decorator', 'Uni/Core/Admin/View/Form/Decorator/', 'decorator');
        $form->setDecorators(array('Tabs'));
        parent::_prepareForm($form);
    }

    /**
     * Retrieve member registration is enabled or not
     * 
     * @return boolean
     */
    public function isRegistrationEnabled() {
        return (Fox::getPreference('member/account/registration') == Fox_Member_Model_Member::MEMBER_REGISTRATION_ENABLED);
    }

    /**
     * Retrieve member registration blocked message set in settings data
     * 
     * @return string
     */
    public function getRegistrationBlockedMessage() {
        return Fox::getPreference('member/account/registration_block_message');
    }

}