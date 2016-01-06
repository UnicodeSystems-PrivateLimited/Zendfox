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
 * Class Uni_Data_XDOMDocument
 * extension of DOMDocument class
 * 
 * @uses        DOMDocument
 * @category    Uni
 * @package     Uni_Data
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Data_XDOMDocument extends DOMDocument {

    /**
     * Constructs new object of Uni_Data_XDOMDocument
     *
     * @param string $version
     * @param string $encoding 
     */
    public function __construct($version='1.0', $encoding='utf-8') {
        parent::__construct($version, $encoding);
    }

    /**
     * Convert xml document to its corresponding php array
     *
     * @param DOMXpath $xpath
     * @return array 
     */
    public function toArray($xpath=null) {
        $data = array();
        if (!is_null($xpath)) {
            $xp = new DOMXPath($this);
            $nodeList = $xp->query($xpath);
            if ($nodeList->length > 1) {
                foreach ($nodeList as $node) {
                    $data[] = $this->getNodeData($node);
                }
            } else {
                foreach ($nodeList as $node) {
                    $data = $this->getNodeData($node);
                }
            }
        } else {
            $data = $this->getNodeData($this);
        }

        return $data;
    }

    /**
     *
     * @param array $data
     * @return string 
     */
    public function loadData(array $data) {
        $xml = '';
        $this->removeChild($this->documentElement);
        $cNode;
        foreach ($data as $k => $v) {
            if (is_numeric($k)) {
                $tNode = $doc->createElement();
            }
        }
        return $xml;
    }

    /**
     * Converts DOMElement object to php array
     *
     * @param DOMElement $element
     * @return array 
     */
    public function getNodeData($element) {
        $data = array();
        $nodeList = $element->childNodes;
        if ($nodeList->length > 0) {
            foreach ($nodeList as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $temp = array();
                    if ($node->hasChildNodes()) {
                        $temp = $this->getNodeData($node);
                    }
                    if ($node->hasAttributes()) {
                        $attr = $this->getAttributeData($node);
                        $temp = array_merge($temp, $attr);
                    }
                    $data[][$node->nodeName] = $temp;
                } else if ($node->nodeType == XML_TEXT_NODE) {
                    if (trim($node->nodeValue) != '') {
                        $data[] = $node->nodeValue;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Retrievs all attributes as php array
     *
     * @param DOMElement $element
     * @return array 
     */
    public static function getAttributeData($element) {
        $data = array();
        $nodeList = $element->attributes;
        foreach ($nodeList as $node) {
            $data[$node->nodeName] = $node->nodeValue;
        }
        return $data;
    }

    /**
     * Created DOMElement from node info
     *
     * @param string $name
     * @param array $attrs
     * @param DOMDocument $doc
     * @return DOMElement 
     */
    public static function createNode($name, $attrs, DOMDocument $doc) {
        $node = $doc->createElement($name);
        if (is_array($attrs)) {
            foreach ($attrs as $attrN => $attrV) {
                $node->setAttribute($attrN, $attrV);
            }
        }
        return $node;
    }

    /**
     * Copy DOMNode in specified document
     *
     * @param DOMNode $node
     * @param DOMDocument $doc
     * @return DOMNode 
     */
    public static function createNodeIn(DOMNode $node, DOMDocument $doc) {
        if ($node->nodeType == XML_ELEMENT_NODE) {
            $cpNode = $doc->createElement($node->tagName);
            if ($node->hasAttributes()) {
                $attrs = $node->attributes;
                foreach ($attrs as $attr) {
                    $cpNode->setAttribute($attr->nodeName, $attr->nodeValue);
                }
            }
        }
        return $cpNode;
    }

    /**
     * Converts DOMElement object into text representation
     *
     * @param DOMElement $ele
     * @param boolean $allowEmpty
     * @return string 
     */
    public static function getNodeAsString(DOMElement $ele, $allowEmpty=TRUE) {
        $nStr = '<' . $ele->nodeName;
        if ($ele->hasAttributes()) {
            $attrs = $ele->attributes;
            foreach ($attrs as $attr) {
                $nStr = $nStr . ' ' . $attr->nodeName . '="' . $attr->nodeValue . '"';
            }
            $nStr = $nStr . ($allowEmpty ? '/>' : '></' . $ele->tagName . '>');
        }
        return $nStr;
    }

}