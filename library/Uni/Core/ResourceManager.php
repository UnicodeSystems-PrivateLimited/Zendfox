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
 * Class Uni_Core_ResourceManager
 * 
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_ResourceManager {

    /**
     * Add resources
     * 
     * @param string $module module name
     */
    public static function addResources($module) {
        if (($doc = Uni_Fox::loadModuleConfig($module))) {
            $sourceQueue = NULL;
            $xPath = new DOMXPath($doc);
            $aclCats = $xPath->query('/config/admin/acl');
            if ($aclCats->length) {
                foreach ($aclCats as $aclCat) {
                    if ($aclCat->childNodes->length) {
                        self::parseConfigACL($aclCat, $module);
                    } else {
                        self::parseConfigACL($module . ' Module', $module, $module . ' Module', $module . ' Module', 0, FALSE);
                    }
                }
            }
        }
    }

    /**
     * Parse config file and save resources
     * 
     * @param string $root
     * @param string $module
     * @param string $path
     * @param string $resource
     * @param int $order
     * @param boolean $isAcl 
     */
    private static function parseConfigACL($root, $module, $path='', $resource='', $order=0, $isAcl=TRUE) {
        $resourceCatModel = Fox::getModel('admin/role/resource/category');
        $resourceModel = Fox::getModel('admin/role/resource');
        $sourceQueue = array(array('cat' => $root, 'path' => $path, 'resource' => $resource, 'order' => $order));
        $mnuPaths = array();
        while (count($sourceQueue) > 0) {
            $accessAll = 0;
            $catResources = array();
            $catInfo = array_shift($sourceQueue);
            $cat = $catInfo['cat'];
            $parentPath = $catInfo['path'];
            if ($isAcl) {
                $mChs = $cat->childNodes;
                if ($mChs->length) {
                    foreach ($mChs as $mCh) {
                        if ($mCh->nodeName == 'cat') {
                            $label = $mCh->getAttribute('label');
                            $menuPath = $parentPath . '/' . str_replace('/', '_', $label);
                            if (isset($mnuPaths[$menuPath])) {
                                
                            } else {
                                $mnuPaths[$menuPath] = $menuPath;
                            }
                            $sourceQueue[] = array('cat' => $mCh, 'path' => $menuPath, 'parent' => $cat->getAttribute('label'), 'resource' => $mCh->getAttribute('resource'), 'order' => $mCh->getAttribute('order'));
                        } else if ($mCh->nodeName == 'resource') {
                            $resourceName = $mCh->getAttribute('name');
                            $actions = $mCh->childNodes;
                            if ($actions->length) {
                                foreach ($actions as $action) {
                                    if ($action->nodeName == 'action') {
                                        $resourceActions = $action->getAttribute('name');
                                        if (($dependents = $action->childNodes)) {
                                            foreach ($dependents as $dependent) {
                                                if ($dependent->nodeName == 'dependents') {
                                                    if (($dependentActions = $dependent->childNodes)) {
                                                        foreach ($dependentActions as $dependentAction) {
                                                            if ($dependentAction->nodeName == 'action') {
                                                                $resourceActions.=',' . $dependentAction->getAttribute('name');
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $resourceModel->load(array('resource' => $resourceName, 'action' => $resourceActions, 'module' => $module));
                                        $resourceModel->setResource($resourceName);
                                        $resourceModel->setResourcePath($action->getAttribute('label'));
                                        $resourceModel->setAction($resourceActions);
                                        $resourceModel->setSortOrder($action->getAttribute('order'));
                                        $resourceModel->setModule($module);
                                        $resourceModel->setDisplayInTree(1);
                                        $resourceModel->save();
                                        $catResources[] = $resourceModel->getId();
                                        $resourceModel->unsetData();
                                    }
                                }
                            } else {
                                $resourceModel->load(array('resource' => $resourceName, 'module' => $module));
                                $resourceModel->setResource($resourceName);
                                $resourceModel->setResourcePath($mCh->getAttribute('label'));
                                $resourceModel->setAction('-1');
                                $resourceModel->setSortOrder($mCh->getAttribute('order'));
                                $resourceModel->setModule($module);
                                $resourceModel->setDisplayInTree(1);
                                $resourceModel->save();
                                $catResources[] = $resourceModel->getId();
                                $resourceModel->unsetData();
                            }
                        }
                    }
                    if (empty($catResources) && !$mChs->length) {
                        $accessAll = 1;
                    }
                } else {
                    $accessAll = 1;
                }
            } else {
                $accessAll = 1;
            }
            $catPath = explode('/', $catInfo['path']);
            $catName = $catPath[count($catPath) - 1];
            if ($catName) {
                $resourceCatModel->load(array('name' => $catName, 'module' => $module));
                $resourceCatModel->setName($catName);
                if (isset($catInfo['parent']) && $catInfo['parent']) {
                    $resourceParentCatModel = Fox::getModel('admin/role/resource/category', true);
                    $resourceParentCatModel->load($catInfo['parent'], 'name');
                    $resourceCatModel->setParentId($resourceParentCatModel->getId());
                    $resourceCatModel->setParentIdPath(($resourceParentCatModel->getParentId() ? ($resourceParentCatModel->getParentIdPath() . ',' . $resourceParentCatModel->getId()) : $resourceParentCatModel->getId()));
                }
                if ($catInfo['resource']) {
                    $resourceModel->load(array('resource' => $catInfo['resource'], 'module' => $module));
                    $resourceModel->setResource($catInfo['resource']);
                    $resourceModel->setResourcePath($catName);
                    $resourceModel->setAction('-1');
                    $resourceModel->setSortOrder(0);
                    $resourceModel->setModule($module);
                    $resourceModel->setDisplayInTree(0);
                    $resourceModel->save();
                    $catResources[] = $resourceModel->getId();
                    $resourceModel->unsetData();
                }
                $resourceCatModel->setResourceIds(implode(',', $catResources));
                $resourceCatModel->setSortOrder($catInfo['order']);
                $resourceCatModel->setModule($module);
                $resourceCatModel->setAccessAll($accessAll);
                $resourceCatModel->save();
                $resourceCatModel->unsetData();
            }
        }
    }
}