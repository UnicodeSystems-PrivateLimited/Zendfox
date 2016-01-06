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
 * Class Contact_IndexController
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Contact
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Contact_IndexController extends Fox_Core_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $model = Fox::getModel('contact/contact');
                $data['status'] = Fox_Contact_Model_Contact::STATUS_UNREAD;
                $data['contact_date'] = Fox_Core_Model_Date::getCurrentDate('Y-m-d H:i:s');
                $model->setData($data);
                $model->save();
                try {
                    Fox::getHelper('core/message')->setInfo("Your request was successfully sent.");
                    $modelTemplate = Fox::getModel('core/email/template');
                    $modelTemplate->sendTemplateMail(Fox::getPreference('contact/receiver/email_template'), Fox::getPreference('contact/receiver/email'), array('sender_name' => $data['name'], 'sender_email' => $data['email']), $data);
                } catch (Exception $e) {}
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

}