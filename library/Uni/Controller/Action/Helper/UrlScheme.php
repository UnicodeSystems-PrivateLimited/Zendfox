<?php

/**
 *  Zendfox Framework
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
 * Class Uni_Controller_Action_Helper_UrlScheme
 * 
 * @uses        Zend_Controller_Action_Helper_Abstract
 * @category    Uni
 * @package     Uni_Controller
 * @subpackage  Uni_Controller_Action_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 * 
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Uni_Controller_Action_Helper_UrlScheme extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Switches url to http/https automatically as per application setting
     * 
     */
    public function direct() {
        $request = $this->getRequest();
        $scheme = $request->getScheme();
        if ($scheme == 'http' && Fox::isSecureUrlEnabled()) {
            $url = 'https://'
                    . $request->getServer('SERVER_NAME')
                    . $request->getRequestUri();
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl($url);
        } else if ($scheme == 'https' && !Fox::isSecureUrlEnabled()) {
            $url = 'http://'
                    . $request->getServer('SERVER_NAME')
                    . $request->getRequestUri();            
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl($url);
        }
    }

}