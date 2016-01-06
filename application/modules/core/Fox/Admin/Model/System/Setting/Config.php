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
 * Class Fox_Admin_Model_System_Setting_Config
 * 
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Model_System_Setting_Config {

    /**
     * Contains final composed menu
     * 
     * @var Uni_Data_XDOMDocument 
     */
    protected $finalMenuTree;
    /**
     * Contains current loaded setting key
     * 
     * @var string 
     */
    protected $item;

    /**
     * @return boolean 
     */
    function getId() {
        return FALSE;
    }

    /**
     * Get current loaded setting key
     * 
     * @return string 
     */
    function getItem() {
        return $this->item;
    }

    /**
     * Retrieve whether the specified setting exists
     * 
     * @param string $key
     * @return boolean 
     */
    public static function isValidSetting($key) {
        $valid = false;
        $modules = Uni_Fox::getModules();
        $doc = new Uni_Data_XDOMDocument();
        if (is_array($modules)) {
            foreach ($modules as $module) {
                $filePath = $module['path'] . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'setting.xml';
                if (file_exists($filePath)) {
                    if ($doc->load($filePath)) {
                        $xPath = new DOMXPath($doc);
                        $setting = $xPath->query('/settings/setting|items');
                        $q = '/settings/items/item[@name="' . $key . '"]/sections';
                        $sections = $xPath->query($q);
                        if ($sections->length > 0) {
                            $valid = true;
                            break;
                        }
                    }
                }
            }
        }
        return $valid;
    }

    /**
     * Loads setting data
     * 
     * @param string $item setting key
     */
    function loadSettingData($item) {
        $this->item = $item;
        $modules = Uni_Fox::getModules();
        $doc = new Uni_Data_XDOMDocument();
        if (is_array($modules)) {
            $this->finalMenuTree = new Uni_Data_XDOMDocument();
            $this->finalMenuTree->formatOutput = true;
            $this->root = Uni_Data_XDOMDocument::createNode('settings', NULL, $this->finalMenuTree);
            $this->finalMenuTree->appendChild($this->root);
            foreach ($modules as $module) {
                $filePath = $module['path'] . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'setting.xml';
                if (file_exists($filePath)) {
                    if ($doc->load($filePath)) {
                        $xPath = new DOMXPath($doc);

                        $setting = $xPath->query('/settings/setting|items');
                        if ($setting->length > 0) {
                            foreach ($setting as $menu) {
                                $this->getFinalMenuTree($menu);
                            }
                        }
                        $q = '/settings/items/item[@name="' . $item . '"]/sections';
                        $sections = $xPath->query($q);
                        if ($sections->length > 0) {
                            foreach ($sections as $section) {
                                $this->getFinalMenuTree($section);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Prepares setting menu
     * 
     * @param DOMElement $menu 
     */
    protected function getFinalMenuTree($menu) {
        $sourceQueue = array($menu);
        $targetQueue = array('root' => $this->root);
        while (count($sourceQueue) > 0) {
            $menu = array_shift($sourceQueue);
            $parent = array_shift($targetQueue);
            if (($mChs = $menu->childNodes)) {
                foreach ($mChs as $mCh) {
                    if ($mCh->nodeType != XML_TEXT_NODE) {
                        $node = $this->finalMenuTree->importNode($mCh, ($mCh->nodeName != 'item'));
                        if (!$node->hasAttribute('order')) {
                            $node->setAttribute('order', 0);
                        }
                        $parent->appendChild($node);
                    }
                }
            }
        }
    }

    /**
     * @return string the XML, or false if an error occurred
     */
    function getMenuMarkup() {
        $menuQueue = array();
        $menuTree = new Uni_Data_XDOMDocument();
        $root = Uni_Data_XDOMDocument::createNode('div', array('id' => 'setting-menu', 'class' => 'accordion'), $menuTree);
        $xPath = new DOMXPath($this->finalMenuTree);
        $menus = $xPath->query('/settings/menu');
        $menus = $this->getSortedNodes($menus);
        foreach ($menus as $menu) {
            $h3 = Uni_Data_XDOMDocument::createNode('h3', array('class' => 'accordion-header'), $menuTree);
            $a = Uni_Data_XDOMDocument::createNode('a', array('href' => '#'), $menuTree);
            $a->appendChild($menuTree->createTextNode($menu->getAttribute('label')));
            $h3->appendChild($a);
            $div = Uni_Data_XDOMDocument::createNode('div', array('class' => 'accordion-content'), $menuTree);
            $ul = Uni_Data_XDOMDocument::createNode('ul', NULL, $menuTree);
            $div->appendChild($ul);
            $root->appendChild($h3);
            $root->appendChild($div);
            $menuQueue[$menu->getAttribute('name')] = $ul;
        }
        $items = $xPath->query('/settings/item');
        $items = $this->getSortedNodes($items);
        foreach ($items as $item) {
            $li = Uni_Data_XDOMDocument::createNode('li', NULL, $menuTree);
            $a = Uni_Data_XDOMDocument::createNode('a', array('href' => Fox::getUrl('admin/system_setting/edit', array('item' => $item->getAttribute('name'),'lbl' => urlencode($item->getAttribute('label'))))), $menuTree);
            $a->appendChild($menuTree->createTextNode($item->getAttribute('label')));
            $li->appendChild($a);
            if (($parent = $menuQueue[$item->getAttribute('menu')])) {
                $parent->appendChild($li);
            }
        }
        return $menuTree->saveXML($root);
    }

    /**
     * Prepares form data for current setting
     * 
     * @return array 
     */
    function getEavFormMetaData() {
        $eavFormMeta = array();
        $data=Fox::getModel('core/session')->getFormData();
        if (!$data) {
            $preferenceModel = Fox::getModel('core/preference');
            $data = $preferenceModel->getPreferenceData($this->item);
        }
        $xPath = new DOMXPath($this->finalMenuTree);
        $sections = $xPath->query('/settings/section');
        $sections = $this->getSortedNodes($sections);
        $i = 0;
        foreach ($sections as $section) {
            if (!($label = $section->getAttribute('label'))) {
                $label = '';
            }
            if (!($sectionName = $section->getAttribute('name'))) {
                $sectionName = $i++;
            }
            if (($mChs = $section->childNodes)) {
                $mChs = $this->getSortedNodes($mChs);
                foreach ($mChs as $mCh) {
                    if ($mCh->nodeName == 'field') {
                        $validation = NULL;
                        $required = FALSE;
                        if (!($order = $mCh->getAttribute('order'))) {
                            $order = 0;
                        }
                        $multiOptions = array();
                        $default = '';
                        $description = '';
                        if (($props = $mCh->childNodes)) {
                            foreach ($props as $prop) {
                                if ($prop->nodeType == XML_ELEMENT_NODE) {
                                    $nodeName = $prop->nodeName;
                                    $$nodeName = $prop->nodeValue;
                                    if ($nodeName == 'options' && ($options = $prop->childNodes)) {
                                        foreach ($options as $option) {
                                            if ($option->nodeType == XML_ELEMENT_NODE) {
                                                $text = $option->nodeValue;
                                                if (!$option->hasAttribute('value')) {
                                                    $value = $text;
                                                } else {
                                                    $value = $option->getAttribute('value');
                                                }
                                                $multiOptions[$value] = $text;
                                            }
                                        }
                                    } else if ($nodeName == 'sourceModel') {
                                        $multiOptions = $this->getMultiOptionsFromSourceModel($sourceModel);
                                    } else if ($nodeName == 'validation' && preg_match("/required/", $prop->nodeValue)) {
                                        $required = TRUE;
                                    }
                                }
                            }
                        }
                        $eavFormMeta[$label][] = array(
                            'attribute_code' => $mCh->getAttribute('name'),
                            'input_type' => $mCh->getAttribute('inputType'),
                            'frontend_label' => $mCh->getAttribute('label'),
                            'validation' => $validation,
                            'is_required' => $required,
                            'position' => $order,
                            'multiOptions' => $multiOptions,
                            'default_value' => $default,
                            'description' => $description,
                            'belongsTo' => $sectionName
                        );
                        if(strtolower($mCh->getAttribute('inputType'))=='multiselect' && is_array($data) && isset($data[$this->item]) && isset($data[$this->item][$sectionName]) && isset($data[$this->item][$sectionName][$mCh->getAttribute('name')])){
                            $data[$this->item][$sectionName][$mCh->getAttribute('name')]=explode(',', $data[$this->item][$sectionName][$mCh->getAttribute('name')]);
                        }
                    }
                }
            }
        }
        Fox::getModel('core/session')->setFormData($data);
        return $eavFormMeta;
    }

    /**
     * Get sorted menu nodes
     * 
     * @param DOMNodeList $nodeList
     * @return array 
     */
    protected function getSortedNodes(DOMNodeList $nodeList) {
        $sorted = array();
        foreach ($nodeList as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $sorted[] = $node;
            }
        }
        usort($sorted, 'Fox_Admin_Model_System_Setting_Config::compareNode');
        return $sorted;
    }

    /**
     * Compares the two nodes on the basis of order attribute
     * @method int compareNode($ob1,$ob2) compare two nodes
     * @param DOMElement $ob1
     * @param DOMElement $ob2
     * @return int 
     */
    protected static function compareNode($ob1, $ob2) {
        return (intval($ob1->getAttribute('order')) < intval($ob2->getAttribute('order')) ? -1 : 1);
    }

    /**
     * Get multi options values
     * 
     * @param string $sourceModel
     * @return array 
     */
    function getMultiOptionsFromSourceModel($sourceModel) {
        $modelParts = explode(':', $sourceModel);
        $model = Fox::getModel($modelParts[0]);
        if ($model) {
            $multiOptions = $model->_getOptionArray(((count($modelParts) > 0) ? array_slice($modelParts, 1) : NULL));
        }
        return isset($multiOptions) ? $multiOptions : array();
    }

}