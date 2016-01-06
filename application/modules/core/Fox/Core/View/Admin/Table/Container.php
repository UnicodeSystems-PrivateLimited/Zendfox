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
 * Class Fox_Core_View_Admin_Table_Container
 * 
 * @uses Fox_Core_View_Admin_Container
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Table_Container extends Fox_Core_View_Admin_Container {

    /**
     * @var string 
     */
    protected $id;
    /**
     * Add button text
     * 
     * @var string
     */
    protected $addButtonText;
    /**
     * Add button action
     * 
     * @var string
     */
    protected $addButtonAction = 'add';
    /**
     * Add button visibility
     * 
     * @var boolean
     */
    protected $isAddButtonEnabled = TRUE;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('fox/containers/table/container');
    }

    /**
     * Get id
     * 
     * @return string
     */
    public function getId() {
        if (NULL == $this->id) {
            $this->id = get_class($this);
        }
        return $this->id;
    }

    /**
     * Set id
     * 
     * @param string $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get add button text
     * 
     * @return string
     */
    public function getAddButtonText() {
        return $this->addButtonText;
    }

    /**
     * Set add button text
     * 
     * @param string $addButtonText 
     */
    public function setAddButtonText($addButtonText) {
        $this->addButtonText = $addButtonText;
    }

    /**
     * Get add button action
     * 
     * @return string
     */
    public function getAddButtonAction() {
        return $this->addButtonAction;
    }

    /**
     * Get add button url
     * 
     * @return string
     */
    public function getAddButtonUrl() {
        return $this->getUrl('*/*/'.$this->addButtonAction);
    }

    /**
     * Set add button action
     * 
     * @param string $addButtonAction 
     */
    public function setAddButtonAction($addButtonAction) {
        $this->addButtonAction = $addButtonAction;
    }

    /**
     * Retrieve the visibility of add button
     * 
     * @return boolean
     */
    public function getIsAddButtonEnabled() {
        return $this->isAddButtonEnabled;
    }

    /**
     * Set the visibilty of add button
     * 
     * @param boolean $isAddButtonEnabled 
     */
    public function setIsAddButtonEnabled($isAddButtonEnabled) {
        $this->isAddButtonEnabled = $isAddButtonEnabled;
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        $contClass = get_class($this);
        if (NULL == $this->id) {
            $this->id = $contClass;
        }
        $tableClass = $contClass . '_Table';
        $tableView = Fox::getView($tableClass);
        if ($tableView) {
            $this->addChild('__table__', $tableView);
        }
    }

    /**
     * Render contents
     * 
     * @return string
     */
    public function renderView() {
        return parent::renderView();
    }

}