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
 * Class IndexController
 * 
 * @uses Fox_Core_Controller_Action
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class IndexController extends Fox_Core_Controller_Action {

    /**
     * Index action
     */
    public function indexAction() {        
        if (($routKey = $this->getRequestUriAfterBaseUrl(NULL,FALSE)) == '') {
            $routKey = 'home';
        }
        $router = Fox::getHelper('core/router');
        if (!$router->routKey($this, $routKey)) {
            $this->_forward('page-not-found');
        }
    }

    /**
     * Page not found action
     */
    public function pageNotFoundAction() {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        if (!Fox::getHelper('core/router')->routKey($this, 'no-page-404')) {
            $this->_forward('nothing');
        }
    }

    /**
     * Nothing action
     */
    public function nothingAction() {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        $this->loadLayout();
        $this->renderLayout();
    }

}