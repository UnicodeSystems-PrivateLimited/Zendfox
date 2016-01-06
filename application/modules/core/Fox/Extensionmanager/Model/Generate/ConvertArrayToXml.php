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
 * Class Fox_Extensionmanager_Model_Generate_ConvertArrayToXml
 * 
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Model_Generate_ConvertArrayToXml {
    /*
     * Contains XML Array
     * @var array
     */
    private $_xmlArray;

    /*
     * array is OK
     * @var bool
     */
    private $_isArrayOk;

    /*
     * Uni_Data_XDOMDocument instance
     * @var DomDocument
     */
    private $document;

    /**
     * Generates Xml of Array
     * @param string $rootName [optional] default 'root'
     * @return String xml string if available otherwise false
     */
    public function saveArrayToXml($rootName = "") {
        $this->document = new Uni_Data_XDOMDocument();
        $arr = array();
        if (count($this->_xmlArray) > 1) {
            if ($rootName != "") {
                $root = $this->document->createElement($rootName);
            } else {
                $root = $this->document->createElement("root");
            }
            $arr = $this->_xmlArray;
        } else {
            $key = key($this->_xmlArray);
            if (!is_int($key)) {
                $root = $this->document->createElement($key);
            } else {
                if ($rootName != "") {
                    $root = $this->document->createElement($rootName);
                } else {
                    $root = $this->document->createElement("root");
                }
            }
            $arr = $this->_xmlArray[$key];
        }
        $root = $this->document->appendchild($root);
        $this->addArray($arr, $root);

        if ($xmlString = $this->document->saveXML()) {
            return $xmlString;
        } else {
            return false;
        }
    }
    
    /**
     * Get generated dom document
     * @return Uni_Data_XDOMDocument
     */
    public function getXMLDom(){
        return $this->document;
    }

    /**
     * parse array to xml node Recursively
     * @param array $arr
     * @param DomNode &$n
     * @param string $path
     */
    protected function addArray($arr, &$n, $path = '') {
        foreach ($arr as $k => $v) {
            $node = $this->document->createElement("item");
            $nodeContent = $this->document->createElement("content");
            $nodeName = $this->document->createElement("name");
            $nodeContent->appendChild($nodeName);
            $node->appendChild($nodeContent);
            if (is_array($v)) {
                $nodeName->appendChild($this->document->createTextNode($k));
                $node->setAttribute("rel", "dir");
                $pathT = $path . DS . $k;
                $node->setAttribute("path", $pathT);
                $this->addArray($arr[$k], $node, $pathT);
            } else {
                $nodeName->appendChild($this->document->createTextNode($v));
                $node->setAttribute("rel", "file");
                $node->setAttribute("path", $path . DS . $v);
            }
            $n->appendChild($node);
        }
    }

    /**
     * Sets Array
     * @param array $xmlArray
     * @return bool
     */
    public function setArray($xmlArray) { 
        if (is_array($xmlArray) && count($xmlArray) != 0) {
            $this->_xmlArray = $xmlArray;
            $this->_isArrayOk = true;
        } else {
            $this->_isArrayOk = false;
        }
        return $this->_isArrayOk;
    }

}
