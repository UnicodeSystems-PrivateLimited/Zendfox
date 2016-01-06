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
 * Class Fox_Admin_View_Cms_Navigation
 * 
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_Cms_Navigation extends Uni_Core_Admin_View {

    /**
     * @var array 
     */
    private $sourceQueue;
    /**
     * @var array
     */
    private $targetQueue;
    /**
     * @var DOMDocument
     */
    private $mnuDoc;
    /**
     * @var DOMDocument
     */
    private $finalMenuTree;
    /**
     * @var array
     */
    private $mnuPaths;
    /**
     * @var DOMElement
     */
    private $root;
    /**
     * @var string
     */
    private $menuHTML = '';

    /**
     * Get navigation html
     * 
     * @return string
     */
    public function getNavigationHtml() {
        $modules = Uni_Fox::getModules();
        $doc = new DOMDocument();
        $this->finalMenuTree = new DOMDocument();
        $this->finalMenuTree->formatOutput = true;
        $this->root = Uni_Data_XDOMDocument::createNode('menus', NULL, $this->finalMenuTree);
        $this->finalMenuTree->appendChild($this->root);
        $this->sourceQueue = array();
        $this->targetQueue = array();
        if (is_array($modules)) {
            foreach ($modules as $module) {
                if ($doc->load($module['path'] . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'config.xml')) {
                    $xPath = new DOMXPath($doc);
                    $menus = $xPath->query('/config/admin/menus');
                    if ($menus->length > 0) {
                        foreach ($menus as $menu) {
                            $this->getFinalMenuTree($menu);
                        }
                    }
                }
            }
        }
        if (isset($this->finalMenuTree) && $this->root->hasChildNodes()) {
            $this->optimizeMenuTree();
            $this->mnuDoc = new DOMDocument();
            $this->mnuDoc->formatOutput = true;
            $this->mnuRoot = Uni_Data_XDOMDocument::createNode('ul', array('class' => 'admin-nav-menu'), $this->mnuDoc);
            $this->mnuDoc->appendChild($this->mnuRoot);
            $this->createMenu($this->root);
            if (isset($this->mnuDoc)) {
                $this->menuHTML = $this->mnuDoc->saveXML($this->mnuRoot);
                return $this->menuHTML;
            }
        }
    }

    /**
     * Removes the nodes with empty child nodes
     */
    private function optimizeMenuTree() {
        $xPath = new DOMXPath($this->finalMenuTree);
        $menus = $xPath->query('//menu[not(.//item)]');
        foreach ($menus as $menu) {
            $menu->parentNode->removeChild($menu);
        }
    }

    /**
     * Create menu html
     * 
     * @param DOMElement $menu
     */
    private function createMenu($menu) {
        $sourceQueue = array($menu);
        $targetQueue = array($this->mnuRoot);
        while (count($sourceQueue) > 0) {
            $menu = array_shift($sourceQueue);
            $parent = array_shift($targetQueue);
            if (($mChs = $this->getSortedNodes($menu->childNodes))) {
                foreach ($mChs as $mCh) {
                    if ($mCh->nodeName == 'item') {
                        $node = Uni_Data_XDOMDocument::createNode('li', NULL, $this->mnuDoc);
                        $link = Uni_Data_XDOMDocument::createNode('a', array('href' => Fox::getUrl($mCh->getAttribute('action'))), $this->mnuDoc);
                        $link->appendChild($this->mnuDoc->createTextNode($mCh->nodeValue));
                        $node->appendChild($link);
                        $parent->appendChild($node);
                    } else if ($mCh->nodeName == 'menu') {
                        $label = $mCh->getAttribute('label');
                        $li = Uni_Data_XDOMDocument::createNode('li', NULL, $this->mnuDoc);
                        $parent->appendChild($li);
                        $span = Uni_Data_XDOMDocument::createNode('a', array('href' => '#'), $this->mnuDoc);
                        $span->appendChild($this->mnuDoc->createTextNode($label));
                        $li->appendChild($span);
                        $ul = Uni_Data_XDOMDocument::createNode('ul', NULL, $this->mnuDoc);
                        $ul->appendChild($this->mnuDoc->createTextNode(''));
                        $li->appendChild($ul);
                        $targetQueue[] = $ul;
                        $sourceQueue[] = $mCh;
                    }
                }
            }
        }
    }

    /**
     * Prepares final menu html
     * 
     * @param DOMElement $menu
     */
    protected function getFinalMenuTree($menu) {
        if (NULL == $this->mnuPaths) {
            $this->mnuPaths = array();
        }
        $sourceQueue = array(array('menu' => $menu, 'path' => 'root'));
        $targetQueue = array($this->root);
        while (count($sourceQueue) > 0) {
            $mnuItem = array_shift($sourceQueue);
            $menu = $mnuItem['menu'];
            $parentPath = $mnuItem['path'];
            $parent = array_shift($targetQueue);
            if (($mChs = $menu->childNodes)) {
                foreach ($mChs as $mCh) {
                    if ($mCh->nodeName == 'item') {
                        if (Fox::getHelper('admin/acl')->isAllowed($mCh->getAttribute('action'))) {
                            $node = Uni_Data_XDOMDocument::createNode('item', array('action' => $mCh->getAttribute('action'), 'order' => (($order = $mCh->getAttribute('order')) ? $order : '0')), $this->finalMenuTree);
                            $node->appendChild($this->finalMenuTree->createTextNode($mCh->nodeValue));
                            $parent->appendChild($node);
                        }
                    } else if ($mCh->nodeName == 'menu') {
                        $label = $mCh->getAttribute('label');
                        $menuPath = $parentPath . '/' . str_replace('/', '_', $label);
                        if (isset($this->mnuPaths[$menuPath])) {
                            $item = $this->mnuPaths[$menuPath];
                        } else {
                            $item = Uni_Data_XDOMDocument::createNode('menu', array('label' => $label, 'order' => (($order = $mCh->getAttribute('order')) ? $order : '0')), $this->finalMenuTree);
                            $parent->appendChild($item);
                            $this->mnuPaths[$menuPath] = $item;
                        }
                        $targetQueue[] = $item;
                        $sourceQueue[] = array('menu' => $mCh, 'path' => $menuPath);
                    }
                }
            }
        }
    }

    /**
     * Get sorted menu nodes
     * 
     * @method int compareNode($ob1,$ob2) compare two nodes
     * @param DOMNodeList $nodeList
     * @return array
     */
    protected function getSortedNodes(DOMNodeList $nodeList) {
        $sorted = array();
        foreach ($nodeList as $node) {
            $sorted[] = $node;
        }
        usort($sorted, 'Fox_Admin_View_Cms_Navigation::compareNode');
        return $sorted;
    }

    /**
     * Compares the two nodes on the basis of order attribute
     * 
     * @param DOMElement $ob1
     * @param DOMElement $ob2
     * @return int 
     */
    protected static function compareNode($ob1, $ob2) {
        return (intval($ob1->getAttribute('order')) < intval($ob2->getAttribute('order')) ? -1 : 1);
    }

}