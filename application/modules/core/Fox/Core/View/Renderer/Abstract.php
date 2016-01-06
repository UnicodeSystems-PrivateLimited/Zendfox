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
 * Class Fox_Core_View_Renderer_Abstract
 * 
 * @uses Uni_Core_Renderer_Interface
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Renderer_Abstract implements Uni_Core_Renderer_Interface {

    /**
     * View options
     * 
     * @var array
     */
    protected $viewOptions = array();
    /**
     * View key
     * 
     * @var string
     */
    protected $viewKey;
    /**
     * View object
     * 
     * @var Uni_Core_View 
     */
    protected $view;

    /**
     * Get view options
     * 
     * @return array
     */
    public function getViewOptions() {
        return $this->viewOptions;
    }

    /**
     * Set view options
     * 
     * @param array $viewOptions 
     */
    public function setViewOptions($viewOptions) {
        $this->viewOptions = $viewOptions;
    }

    /**
     * Get view key
     * 
     * @return string
     */
    public function getViewKey() {
        return $this->viewKey;
    }

    /**
     * Set view key
     * 
     * @param string $viewKey 
     */
    public function setViewKey($viewKey) {
        $this->viewKey = $viewKey;
    }

    /**
     * Get view object
     * 
     * @return Uni_Core_View 
     */
    public function getView() {
        return $this->view;
    }

    /**
     * Set view object
     * 
     * @param Uni_Core_View $view 
     */
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * Generates view content
     */
    public function render() {
        
    }

}
