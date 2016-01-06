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
 * Class Fox_Core_Helper_Router
 * 
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Helper_Router {
    /**
     * Whether the routing has been completed or not
     *
     * @var boolean 
     */
    private $routingCompleted = FALSE;

    /**
     * Loades cms page of specified key
     *
     * @param Fox_Core_Controller_Action $controller
     * @param string $key
     * @return boolean 
     */
    public function routKey($controller, $key) {
        $page = Fox::getModel('cms/page');
        $page->load($key, 'url_key');
        if ($page->getId() && $page->getStatus()) {
            if (($container = $page->getLayout()) != '') {
                $layoutUpdate = '<container key="' . $container . '"/>';
            }
            if (($lUpdate = $page->getLayoutUpdate()) != '') {
                $layoutUpdate.=$lUpdate;
            }
            $layout = $controller->loadLayout($layoutUpdate, $key);
            $layout->setTitle($page->getTitle());
            $layout->setMetaKeywords($page->getMetaKeywords());
            $layout->setMetaDescription($page->getMetaDescription());
            if (($contentView = $controller->getViewByKey('content'))) {
                $pageView = Fox::getView('cms/page');
                $pageView->setPageContent($page->getContent());
                $pageView->setPageTitle($page->getTitle());
                $pageView->setPageUrlKey($page->getUrlKey());
                $contentView->addChild('__page_content__', $pageView);
            }
            $controller->renderLayout();
            $controller->getResponse()->setHeader('HTTP/1.1', '200 ok');
            $controller->getResponse()->setHeader('Status', '200 ok');
            $this->routingCompleted = TRUE;
        }
        return $this->routingCompleted;
    }

}
