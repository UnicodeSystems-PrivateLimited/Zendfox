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
 * Class Uni_Core_LayoutManager
 * loads and prapers layout defined for particular action
 *
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_LayoutManager {
    
    /**#@+
     * XML tags used in layout composing constants
     */
    const ROOT='layout';
    const CONTAINER='container';
    const VIEW='view';
    const HEAD='head';
    /**#@-*/

    /**
     * Holds all rendered html contents
     *
     * @var string 
     */
    private $content;
    
    /**
     *
     * @var Uni_Data_XDOMDocument 
     */
    private $xDomFox;
    
    /**
     *
     * @var Uni_Data_XDOMDocument 
     */
    private $xDomMain;
    
    /**
     *
     * @var string 
     */
    private $baseUrl;
    
    /**
     * Module loaded by application
     *
     * @var string 
     */
    private $module;
    
    /**
     * Controller loaded by application
     *
     * @var string 
     */
    private $controller;
    
    /**
     * Action loaded by application
     *
     * @var string 
     */    
    private $action;
    
    /**
     * Application mode (web, admin or installer)
     *
     * @var string 
     */
    private $mode;
    
    /**
     *
     * @var string 
     */
    private $defaultPackage = 'core';
    
    /**
     *
     * @var string 
     */
    private $package = 'core';
    
    /**
     *
     * @var string 
     */
    private $defaultTheme = 'default';
    
    /**
     *
     * @var string 
     */
    private $theme = 'default';
    
    /**
     *
     * @var Uni_Core_View 
     */
    private $container;
    
    /**
     *
     * @var Uni_Core_Layout 
     */
    private $layout;
    
    /**
     *
     * @var Uni_Data_XDOMDocument 
     */
    private $finalLayout;
    
    /**
     *
     * @var array 
     */
    public $viewList = array();
    
    /**
     *
     * @var Uni_Core_Helper 
     */
    private $headHelper;

    /**
     * Constructs the object of Uni_Core_LayoutManager class
     * to manage the layout
     *
     * @param string $baseUrl
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param string $mode
     * @param string $package
     * @param string $theme 
     */
    function __construct($baseUrl, $module, $controller, $action, $mode, $package, $theme) {
        $this->baseUrl = $baseUrl;
        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        $this->mode = $mode;
        if ($package) {
            $this->package = $package;
        }
        if ($theme) {
            $this->theme = $theme;
        }
    }

    /**
     * Loades layout defined for particular action
     *
     * @param string $layoutUpdate
     * @param string $updateKey 
     * @return void
     */
    public function load($layoutUpdate=NULL, $updateKey=NULL) {
        $this->finalLayout = new Uni_Data_XDOMDocument();
        $this->finalLayout->preserveWhiteSpace = FALSE;
        $this->finalLayout->formatOutput = TRUE;
        $ds = DIRECTORY_SEPARATOR;
        $debug = FALSE;
        if(Uni_Core_Installer::isInstalled()){
        $cacheSettings = Uni_Core_CacheManager::loadCacheSettings();
        $isCacheEnabled = (isset($cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_LAYOUT]) ? $cacheSettings[Fox_Core_Model_Cache::CACHE_CODE_LAYOUT] : FALSE);
        $cacheLayoutBasePath = CACHE_DIR . $ds . Fox_Core_Model_Cache::CACHE_CODE_LAYOUT . $ds . $this->mode . $ds . $this->package . $ds . $this->theme;
        (file_exists($cacheLayoutBasePath) && is_dir($cacheLayoutBasePath)) || @mkdir($cacheLayoutBasePath, 0777, TRUE);
        $cacheLayoutPath = $cacheLayoutBasePath . $ds . strtolower($this->module) . '_' . strtolower($this->controller) . '_' . strtolower($this->action) . (isset($updateKey) ? '_' . urlencode($updateKey) : '') . '.lxml';
        }else{
            $isCacheEnabled=FALSE;
        }
        if (!$isCacheEnabled || ($isCacheEnabled && !file_exists($cacheLayoutPath))) {
            $debug || ob_start();            
            $this->layout = new Uni_Core_Layout();
            $this->layout->init();
            $lPaths[] = 'application' . $ds . 'views' . $ds . $this->mode . $ds . $this->package . $ds . $this->theme . $ds . 'layout';
            $lPaths[] = 'application' . $ds . 'views' . $ds . $this->mode . $ds . $this->defaultPackage . $ds . $this->defaultTheme . $ds . 'layout';
            $loadLayout = FALSE;
            foreach ($lPaths as $lBPath) {
                if (file_exists($lBPath . $ds . 'fox.xml')) {
                    $lDPath = $lBPath . $ds . 'fox.xml';
                    break;
                }
            }
            if (isset($lDPath)) {
                $this->xDomFox = new Uni_Data_XDOMDocument();
                $this->xDomFox->load($lDPath);
            } else {
                throw new Exception('Layout not found "' . $lDPath . '"');
            }
            $this->layout->setLayout($this->xDomFox);
            /*             * **************Parsing default Section from all other layouts************** */
            $modules = array_keys(Uni_Fox::getModules());
            foreach ($modules as $module) {
                if ($module != $this->module) {
                    foreach ($lPaths as $lBPath) {
                        if (file_exists($lBPath . $ds . lcfirst($module) . '.xml')) {
                            $lPath = $lBPath . $ds . lcfirst($module) . '.xml';
                            break;
                        }
                    }
                    if (isset($lPath)) {
                        $mLXmlDoc = new Uni_Data_XDOMDocument();
                        $mLXmlDoc->load($lPath);
                        $xpMLXmlDoc = new DOMXPath($mLXmlDoc);
                        $defMLXml = $xpMLXmlDoc->query('/' . self::ROOT . '/global');
                        if ($defMLXml->length > 0) {
                            $this->layout->mergeLayout($defMLXml->item(0), TRUE);
                            $this->layout->showXML();
                        }
                        unset($lPath);
                    }
                }
            }
            foreach ($lPaths as $lBPath) {
                if (file_exists($lBPath . $ds . lcfirst($this->module) . '.xml')) {
                    $lPath = $lBPath . $ds . lcfirst($this->module) . '.xml';
                    break;
                }
            }
            if (isset($lPath)) {
                $this->xDomMain = new Uni_Data_XDOMDocument();
                $this->xDomMain->load($lPath);
            } else {
                throw new Exception('Layout not found "' . $lPath . '"');
            }
            $xpMain = new DOMXPath($this->xDomMain);
            $defGlobal = $xpMain->query('/' . self::ROOT . '/global');
            if ($defGlobal->length > 0) {
                $this->layout->mergeLayout($defGlobal->item(0), TRUE);
            }
            $defMain = $xpMain->query('/' . self::ROOT . '/default');
            if ($defMain->length > 0) {
                $this->layout->mergeLayout($defMain->item(0), TRUE);
            }
            $actMain = $xpMain->query('/' . self::ROOT . '/' . $this->controller . '_' . $this->action);
            if ($actMain->length > 0) {
                $this->layout->mergeLayout($actMain->item(0), TRUE);
            }
            if (isset($layoutUpdate) && isset($updateKey)) {
                $domUpdate = new Uni_Data_XDOMDocument();
                if (!$domUpdate->loadXML('<layout>' . $layoutUpdate . '</layout>')) {
                }
                $this->layout->mergeLayout($domUpdate->documentElement, TRUE);
            }
            $this->layout->prepareHead();
            $this->layout->showXML();
            $this->finalLayout->loadXML($this->layout->saveXML());
            if ($isCacheEnabled) {
                $this->finalLayout->save($cacheLayoutPath);
                @chmod($cacheLayoutPath, 0777);
            }
            unset($this->layout);
            $debug || ob_clean();
        } else if (file_exists($cacheLayoutPath)) {
            $this->finalLayout->load($cacheLayoutPath);
        }
        $this->parse($this->finalLayout);
    }

    /**
     *
     * @param Uni_Data_XDOMDocument $xDomDoc 
     * @return void
     */
    private function parse(Uni_Data_XDOMDocument $xDomDoc) {
        $xPath = new DOMXPath($xDomDoc);
        $res = $xPath->query('/' . self::ROOT . '/' . self::CONTAINER);
        if ($res->length > 0) {
            if (($tpl = $res->item(0)->getAttribute('tpl'))) {
                $this->container = Fox::getView('core/container', $res->item(0)->getAttribute('key'));
                $this->container->setTemplate($tpl);
            }
        }
        /*         * *********************Loading Sections ************************ */
        $this->loadAll($xDomDoc->documentElement);
    }

    /**
     *
     * @param DOMElement $ele 
     * @return void
     */
    private function loadAll(DOMElement $ele) {
        $viewStack = array($ele);
        $this->viewList['root'] = $this->container;
        for ($i = 0; count($viewStack) > 0; $i++) {
            $cont = TRUE;
            $node = array_pop($viewStack);
            if ($i == 0) {
                $pKey = 'root';
            } else if (($pKey = $node->getAttribute('key'))) {
                
            } else {
                $cont = FALSE;
            }
            if ($cont) {
                $parent = $this->viewList[$pKey];
                if ($node->hasChildNodes()) {
                    $nList = $node->childNodes;
                    $nList = $this->getSortedNodes($nList);
                    foreach ($nList as $n) {
                        if ($n->nodeType == XML_ELEMENT_NODE) {
                            if ($n->nodeName == self::VIEW) {
                                if (!($class = $n->getAttribute('class'))) {
                                    $class = 'core/template';
                                }
                                $key = $n->getAttribute('key');
                                $view = Fox::getView($class, $key);
                                if (($tpl = $n->getAttribute('tpl'))) {
                                    $view->setTemplate($tpl);
                                }
                                $parent->addChild($key, $view);
                                $this->viewList[$key] = $view;
                                array_push($viewStack, $n);
                            } else if ($n->nodeName == self::HEAD) {
                                $parent->addHead($n->nodeValue);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Starts rendering of the view objects
     * 
     * @return void
     */
    public function renderAll() {
        $this->content = $this->container->renderView();
    }

    /**
     *
     * @return Uni_Core_Helper 
     */
    public function getHeadHelper() {
        if (NULL == $this->headHelper) {
            $this->headHelper = Fox::getHelper('core/head');
        }
        return $this->headHelper;
    }

    /**
     * Sets title of current rendered page
     *
     * @param string $title 
     * @return void
     */
    public function setTitle($title) {
        $this->getHeadHelper()->setTitle($title);
    }

    /**
     * Appends title in exsisting title of current rendered page
     *
     * @param string $title
     * @param boolean $append 
     * @return void
     */
    public function addTitle($title, $append=TRUE) {
        $this->getHeadHelper()->addTitle($title, $append);
    }

    /**
     * Sets meta-keywords of current loaded page
     *
     * @param string $keywords 
     * @return void
     */
    public function setMetaKeywords($keywords) {
        $this->getHeadHelper()->setMetaKeywords($keywords);
    }

    /**
     * Appends meta-keywords in exsisting meta-keywords of current loaded page
     *
     * @param string $keywords
     * @param bolean $append 
     * @return void
     */
    public function addMetaKeywords($keywords, $append=TRUE) {
        $this->getHeadHelper()->addMetaKeywords($keywords, $append);
    }

    /**
     * Sets meta-description of current loaded page
     *
     * @param string $description 
     * @return void
     */
    public function setMetaDescription($description) {
        $this->getHeadHelper()->setMetaDescription($description);
    }

    /**
     * Appends meta-description in exsisting meta-description of current loaded page
     *
     * @param string $description
     * @param bolean $append 
     * @return void
     */
    public function addMetaDescription($description, $append=TRUE) {
        $this->getHeadHelper()->addMetaDescription($description, $append);
    }

    /**
     * Displays final rendered contents
     * 
     * @return void
     */
    public function echoContent() {
        echo $this->content;
    }

    /**
     * Sorts layout's view elements's order
     *
     * @param DOMNodeList $nodeList
     * @return DOMNodeList 
     */
    protected function getSortedNodes(DOMNodeList $nodeList) {
        $sorted = array();
        foreach ($nodeList as $node) {
            $sorted[] = $node;
        }
        usort($sorted, 'Uni_Core_LayoutManager::compareNode');
        return $sorted;
    }

    /**
     * Callback for sorting
     *
     * @param DOMElement $ob1
     * @param DOMElement $ob2
     * @return int 
     */
    protected static function compareNode($ob1, $ob2) {
        return (intval($ob1->getAttribute('order')) < intval($ob2->getAttribute('order')) ? -1 : 1);
    }
    
}