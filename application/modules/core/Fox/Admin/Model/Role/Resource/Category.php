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
 * Class Fox_Admin_Model_Role_Resource_Category
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_Model_Role_Resource_Category extends Uni_Core_Model {

    /**
     * Public constructor
     */
    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'admin_role_resource_category';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }

    /**
     * Return all categories data
     * 
     * @return Uni_Core_Model_Collection 
     */
    public function getCategories() {
        return $this->getCollection();
    }

    /**
     * Constructs the resources XML and Dumps tree back into a string
     * 
     * @param int $roleId
     * @param int $parentId
     * @return string the XML, or false if an error occurred 
     */
    public function getTreeData($roleId, $parentId=0) {
        $catData = array();
        $model = Fox::getModel('admin/role/resource');
        $roleModel = Fox::getModel('admin/role');
        $roleModel->load($roleId);
        $roleResources = explode(',', $roleModel->getResourceIds());
        $sourceNodes[] = array('id' => $parentId);
        $mnuDoc = new DOMDocument();
        $mnuRoot = Uni_Data_XDOMDocument::createNode('root', array(), $mnuDoc);
        $mnuDoc->appendChild($mnuRoot);
        while (count($sourceNodes) > 0) {
            $parentData = array_pop($sourceNodes);
            $parentId = $parentData['id'];
            $rootNodes = $this->getCollection("parent_id=$parentId", '*', 'sort_order ASC');
            foreach ($rootNodes as $rootNode) {
                $itemNode = Uni_Data_XDOMDocument::createNode('item', array('id' => ($rootNode['access_all'] == 1 ? ($rootNode['resource_ids']) : ('cat_' . $rootNode['id'])), 'parent_id' => (isset($parentData['treeId']) ? $parentData['treeId'] : 0), 'rel' => 'resource_category', 'state' => 'open'), $mnuDoc);
                $contentNode = Uni_Data_XDOMDocument::createNode('content', array(), $mnuDoc);
                $dataNode = Uni_Data_XDOMDocument::createNode('name', array(), $mnuDoc);
                $dataNode->appendChild($mnuDoc->createTextNode($rootNode['name']));
                $contentNode->appendChild($dataNode);
                $itemNode->appendChild($contentNode);
                $mnuRoot->appendChild($itemNode);
                if ($rootNode['resource_ids']) {
                    $resourceCollection = $model->getCollection('id IN(' . $rootNode['resource_ids'] . ') AND display_in_tree=1', '*', 'sort_order ASC');
                    foreach ($resourceCollection as $resource) {
                        $itemNode = Uni_Data_XDOMDocument::createNode('item', array('id' => $resource['id'], 'rel' => 'resource', 'parent_id' => ($rootNode['access_all'] == 1 ? ($rootNode['resource_ids']) : ('cat_' . $rootNode['id'])), 'state' => 'open'), $mnuDoc);
                        $contentNode = Uni_Data_XDOMDocument::createNode('content', array(), $mnuDoc);
                        $dataNode = Uni_Data_XDOMDocument::createNode('name', array(), $mnuDoc);
                        $dataNode->appendChild($mnuDoc->createCDATASection($resource['resource_path']));
                        $contentNode->appendChild($dataNode);
                        $itemNode->appendChild($contentNode);
                        $mnuRoot->appendChild($itemNode);
                    }
                }

                array_push($sourceNodes, array('id' => $rootNode['id'], 'treeId' => ($rootNode['access_all'] == 1 ? ($rootNode['resource_ids']) : ('cat_' . $rootNode['id']))));
            }
        }
        return $mnuDoc->saveXML($mnuRoot);
    }

}