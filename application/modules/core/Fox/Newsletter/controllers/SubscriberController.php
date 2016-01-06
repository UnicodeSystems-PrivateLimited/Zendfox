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
 * Class Newsletter_SubscriberController
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Newsletter_SubscriberController extends Fox_Core_Controller_Action {

    /**
     * Subscribe action
     */
    function subscribeAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $subscriberModel = Fox::getModel('newsletter/subscriber');
            try {
                $subscriberModel->subscribe($data);
                if (Fox::getPreference('newsletter/subscription/need_to_confirm')) {
                    Fox::getHelper('core/message')->setInfo('Confirmation request has been sent to your email.');
                } else {
                    Fox::getHelper('core/message')->setInfo('Thank you for your subscription.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError('There was a problem with the subscription.');
            }
        }
        $this->redirectReferer();
    }

    /**
     * Confirm subscription action
     */
    function confirmAction() {
        $id = $this->getRequest()->getParam('id', FALSE);
        $code = $this->getRequest()->getParam('code', FALSE);
        if ($id && $code) {
            try {
                $subscriber = Fox::getModel('newsletter/subscriber');
                $subscriber->load($id);
                if ($subscriber->getId() && $subscriber->getCode()) {
                    if ($subscriber->confirm($code)) {
                        Fox::getHelper('core/message')->setInfo('Your subscription was successfully confirmed.');
                    } else {
                        Fox::getHelper('core/message')->setError('Invalid subscription confirmation code.');
                    }
                } else {
                    Fox::getHelper('core/message')->setError('Invalid subscription ID.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError('There was a problem with subscription confirmation.');
            }
        }
        $this->sendRedirect('');
    }

    /**
     * Unsubscribe action
     */
    public function unsubscribeAction() {
        $id = $this->getRequest()->getParam('id', FALSE);
        $code = $this->getRequest()->getParam('code', FALSE);
        if ($id && $code) {
            try {
                $subscriber = Fox::getModel('newsletter/subscriber');
                $subscriber->load($id);
                if ($code == $subscriber->getCode()) {
                    if ($subscriber->unsubscribe()) {
                        Fox::getHelper('core/message')->setInfo('You have been successfully unsubscribed.');
                    }
                } else {
                    Fox::getHelper('core/message')->setError('Invalid subscription confirmation code');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError('There was a problem with the un-subscription.');
            }
        }
        $this->sendRedirect('');
    }

}