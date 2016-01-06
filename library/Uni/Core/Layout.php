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
 * Class Uni_Core_Layout
 * 
 * @uses        Uni_Data_XDOMDocument
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Layout extends Uni_Data_XDOMDocument {
    
    /**#@+
     * XML tags used in layout composing constants
     */
    const SCRIPT='script';
    const STYLE='style';
    const LINK='link';
    const DEFF='define';
    const REF='ref';
    const REMOVE='remove';
    const TITLE='title';
    const META_KEYWORD='keyword';
    const META_DESCRIPTION='description';
    const _GLOBAL='_global';
    /**#@-*/
    
    /**
     * Main layout container
     *
     * @var array
     */
    protected $container = array();
    
    /**
     *
     * @var array 
     */
    protected $viewsRef = array();
    
    /**
     * Collects head data
     *
     * @var array 
     */
    protected $headData = array();
    
    /**
     *
     * @var array 
     */
    protected $eleList;
    
    /**
     *
     * @var array 
     */
    protected $eleStack;
    
    /**
     *
     * @var DOMElement 
     */
    protected $root;
    
    /**
     *
     * @var DOMXPath 
     */
    protected $xPath;
    
    /**
     *
     * @var array 
     */
    protected $defContainer;
    
    /**
     *
     * @var string 
     */
    protected $baseUrl;
    
    /**
     * Constructs object of Uni_Core_Layout
     * @param string $baseUrl      
     */
    function __construct($baseUrl = NULL) {
        $this->baseUrl = $baseUrl;        
        parent::__construct('1.0', 'UTF-8');
    }

    /**
     *  Initilizes Uni_Core_Layout's Object.
     * 
     * @return void
     */
    function init() {
        $root = $this->createElement('layout');
        $this->root = $this->appendChild($root);
        $this->xPath = new DOMXPath($this);
    }

    /**
     *
     * @param string $tpl 
     * @return void
     */
    protected function setContainer($tpl) {
        $nList = $this->xPath->query('/' . Uni_Core_LayoutManager::ROOT . '/' . Uni_Core_LayoutManager::CONTAINER);
        if ($nList->length > 0) {
            $nList->item(0)->setAttribute('tpl', $tpl);
        } else {
            $this->root->appendChild(self::createNode(Uni_Core_LayoutManager::CONTAINER, array('tpl' => $tpl), $this));
        }
    }

    /**
     *
     * @param Uni_Data_XDOMDocument $xDoc 
     * @return void
     */
    public function setLayout(Uni_Data_XDOMDocument $xDoc) {
        $xPathFox = new DOMXPath($xDoc);
        /*        Loading Definitions         */
        $defNodes = $xPathFox->query('/' . Uni_Core_LayoutManager::ROOT . '/' . self::DEFF . '/containers/' . Uni_Core_LayoutManager::CONTAINER);
        $this->defContainer = array();
        foreach ($defNodes as $dn) {
            $dnAttr = $xDoc->getAttributeData($dn);
            $this->defContainer[$dnAttr['key']] = $dnAttr['tpl'];
        }
        /*         * ************Parsing fox.xml***************************** */
        $this->parse($xDoc->documentElement, TRUE);
    }

    /**
     * 
     * @param DOMElement $ele
     * @param boolean $updateContainer default false
     * @return void
     */
    public function mergeLayout(DOMElement $ele, $updateContainer=FALSE) {
        $this->parse($ele, $updateContainer);
    }

    /**
     * Parses XML Layout files used to configure and render views
     *
     * @param DOMElement $ele
     * @param boolean $updateContainer 
     * @return void
     */
    private function parse(DOMElement $ele, $updateContainer) {
        $this->eleStack = array($ele);
        if (empty($this->eleList)) {
            $this->eleList = array('root' => $this->root);
        }
        for ($i = 0; count($this->eleStack) > 0; $i++) {
            $cont = TRUE;
            $node = array_shift($this->eleStack);
            if (($pKey = $node->getAttribute('key'))) {
                if (!array_key_exists($pKey, $this->eleList)) {
                    $cont = FALSE;
                }
            } else {
                $pKey = 'root';
            }
            if ($cont) {
                $parent = $this->eleList[$pKey];
                if ($node->hasChildNodes()) {
                    $nList = $node->childNodes;
                    $tempQ = array();
                    foreach ($nList as $n) {
                        if ($n->nodeName == Uni_Core_LayoutManager::VIEW) {
                            $cpNode = Uni_Data_XDOMDocument::createNodeIn($n, $this);
                            $parent->appendChild($cpNode);
                            if (($key = $cpNode->getAttribute('key'))) {
                                if (array_key_exists($key, $this->eleList)) {
                                    throw new Exception('Duplicte key was found:"' . $key . '"');
                                }
                                $tempQ[$key] = $n;
                                $this->eleList[$key] = $cpNode;
                            }
                        } else if ($updateContainer && $n->nodeName == Uni_Core_LayoutManager::CONTAINER) {
                            if ($n->hasAttributes()) {
                                $nKey = $n->attributes->getNamedItem('key');
                                if ($nKey) {
                                    $this->setContainer($this->defContainer[$nKey->nodeValue]);
                                }
                            }
                        } else if ($n->nodeName == Uni_Core_LayoutManager::HEAD) {
                            $this->parseHead($n);
                        } else if ($n->nodeName == self::REF && ($key = $n->getAttribute('key'))) {
                            $tempQ[] = $n;
                        } else if ($n->nodeName == self::REMOVE && ($key = $n->getAttribute('key'))) {
                            if (array_key_exists($key, $this->eleList)) {
                                try {
                                    $parent->removeChild($this->eleList[$key]);
                                    unset($this->eleList[$key]);
                                    if (array_key_exists($key, $tempQ)) {
                                        unset($tempQ[$key]);
                                    }
                                } catch (Exception $e) {
                                }
                            } else {
                            }
                        }
                    }
                    $this->eleStack = array_merge($this->eleStack, array_values($tempQ));
                }
            }
        }
    }

    /**
     * Prepares head contents included in layout
     * 
     * @return void
     */
    public function prepareHead() {
        $headCData = $this->createCDATASection('');
        foreach ($this->headData as $k => $node) {
            $de = $node['node'];
            $nodeAsText = $de->ownerDocument->saveXML($de);
            if (($if = $node['node']->getAttribute('if'))) {
                $headCData->appendData('<!--[if ' . $if . ']>');
                $node['node']->removeAttribute('if');
                $headCData->appendData($nodeAsText);
                $headCData->appendData('<![endif]-->');
            } else {
                $headCData->appendData($nodeAsText);
            }
            $headCData->appendData("\n");
        }
        $head = self::createNode(Uni_Core_LayoutManager::HEAD, NULL, $this);
        $this->root->appendChild($head);
        $head->appendChild($headCData);
    }

    /**
     * Parses head contents
     *
     * @param DOMNode $node 
     * @return void
     */
    private function parseHead(DOMNode $node) {
        if ($node->hasChildNodes()) {
            $nList = $node->childNodes;
            foreach ($nList as $n) {
                if ($n->nodeName == self::REMOVE) {
                    if (($key = $n->getAttribute('key'))) {
                        if (array_key_exists($key, $this->headData)) {
                            unset($this->headData[$key]);
                        } else {
                        }
                    }
                } else if ($n->nodeName == self::TITLE) {
                    if ($n->getAttribute('type')=='append') {
                        Fox::getHelper('core/head')->addTitle($n->nodeValue, TRUE);
                    }else if ($n->getAttribute('type')=='prepend') {
                        Fox::getHelper('core/head')->addTitle($n->nodeValue, FALSE);
                    }else{
                        Fox::getHelper('core/head')->setTitle($n->nodeValue);
                    }
                } else if ($n->nodeName == self::META_DESCRIPTION) {
                    if ($n->getAttribute('type')=='append') {
                        Fox::getHelper('core/head')->addMetaDescription($n->nodeValue, TRUE);
                    }else if ($n->getAttribute('type')=='prepend') {
                        Fox::getHelper('core/head')->addMetaDescription($n->nodeValue, FALSE);
                    }else{
                        Fox::getHelper('core/head')->setMetaDescription($n->nodeValue);
                    }
                } else if ($n->nodeName == self::META_KEYWORD) {
                    if ($n->getAttribute('type')=='append') {
                        Fox::getHelper('core/head')->addMetaKeywords($n->nodeValue, TRUE);
                    }else if ($n->getAttribute('type')=='prepend') {
                        Fox::getHelper('core/head')->addMetaKeywords($n->nodeValue, FALSE);
                    }else{
                        Fox::getHelper('core/head')->setMetaKeywords($n->nodeValue);
                    }
                } else {
                    if ($n->hasAttributes()) {
                        if ($n->nodeName == self::SCRIPT) {
                            $n->appendChild($n->ownerDocument->createTextNode(''));
                            $kSrc = 'src';
                        } else {
                            $kSrc = 'href';
                        }
                        $nSrc = $n->getAttribute($kSrc);
                        if ($n->hasAttribute(self::_GLOBAL)) {
                            $n->setAttribute($kSrc, Fox::getSiteUrl() . $nSrc);
                            $n->removeAttribute(self::_GLOBAL);
                        } else {
                            $n->setAttribute($kSrc, Fox::getThemeUrl($nSrc));
                        }
                        if ($nSrc) {
                            $this->headData[$nSrc] = array('type' => $kSrc, 'node' => $n);
                        } else {
                            $this->headData[] = array('type' => $kSrc, 'node' => $n);
                        }
                    }
                }
            }
        }
    }

    /**
     * Display final prepared layout
     * 
     * @return void
     */
    public function showXML() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->saveXML());
        echo $dom->saveXML();
    }

}
