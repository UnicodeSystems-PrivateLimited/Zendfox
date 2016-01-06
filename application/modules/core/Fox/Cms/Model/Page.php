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
 * Class Fox_Cms_Model_Page
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_Model_Page extends Uni_Core_Model {
    
    const STATUS_ENABLED=1;
    const STATUS_DISABLED=0;

    public function __construct() {
        /**
         * Table name
         */
        $this->_name = 'cms_page';
        /**
         * Table primary key field
         */
        $this->primary = 'id';
        parent::__construct();
    }    
    /**
     * Get page preview url
     * 
     * @return string
     */
    public function getPageAction() {
        return Fox::getUrl($this->getUrlKey());
    }
    /**
     * Get all status
     * 
     * @return array
     */
    public function getAllStatuses() {
        return array('' => '', self::STATUS_ENABLED => 'Enabled', self::STATUS_DISABLED => 'Disabled');
    }
    /**
     * Process data before save
     * 
     * @param array $data
     * @return array 
     * @throws Exception if url key already exists
     */
    protected function _preSave(array $data) {
        if ($this->isDuplicateUrlKey($data['url_key'], $data['id'] ? $data['id'] : 0)) {
            throw new Exception("'" . $data['url_key'] . "' already exists.");
        }
        return $data;
    }
    /**
     * Checks whether the url key already exists
     * 
     * @param string $urlKey
     * @param int $id
     * @return boolean TRUE if url key already exists
     */
    private function isDuplicateUrlKey($urlKey, $id=0) {
        $collection = $this->getCollection(array('url_key' => $urlKey, 'id' => array('operator' => '!=', 'value' => $id)));
        return count($collection)?TRUE:FALSE;
    }
    /**
     * Get available layouts
     * 
     * @return array
     */
    public function getAvailableLayouts() {
        return array('' => '', 'no-layout' => 'No Layout', 'one-column' => 'One Column', 'two-columns-left' => 'Two Columns Left', 'two-columns-right' => 'Two Columns Right', 'three-columns' => 'Three Columns');
    }
    /**
     * Get active pages
     * 
     * @param array $fields
     * @return array
     */
    public function _getOptionArray(array $fields) {
        $options = array('' => 'Select Pages');
        $collection = $this->getCollection('status=' . self::STATUS_ENABLED, '*', 'title ASC');
        foreach ($collection as $page) {
            $options[$page['id']] = $page['title'];
        }
        return $options;
    }

}