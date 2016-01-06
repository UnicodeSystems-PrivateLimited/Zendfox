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
 * Class Fox_Core_Helper_Head
 * 
 * @uses Uni_Core_Helper
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Helper_Head extends Uni_Core_Helper {

    /**
     *
     * @var string 
     */
    private $html;
    /**
     * Page title
     * 
     * @var array
     */
    private $title = array();
    /**
     * Meta keywords
     * 
     * @var array
     */
    private $metaKeywords = array();
    /**
     * Meta description
     * 
     * @var array
     */
    private $metaDescription = array();

    /**
     * Add html
     * 
     * @param string $html 
     */
    public function addHTML($html) {
        $this->html.=$html;
    }

    /**
     * Get html
     * 
     * @return string
     */
    public function getHTML() {
        return $this->html;
    }

    /**
     * Get title
     * 
     * @return array
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set title
     * 
     * @param string $title 
     */
    public function setTitle($title) {
        $this->title = array();
        $this->title[] = $title;
    }

    /**
     * Add title
     * 
     * @param string $title
     * @param boolean $append TRUE adds the title to the last of the array|FALSE adds the title to the start of the array
     */
    public function addTitle($title, $append=TRUE) {
        if ($append) {
            array_push($this->title, $title);
        } else {
            array_unshift($this->title, $title);
        }
    }

    /**
     * Get meta keywords
     * 
     * @return string
     */
    public function getMetaKeywords() {
        if (is_array($this->metaKeywords)) {
            return implode(', ', $this->metaKeywords);
        }
        return $this->metaKeywords;
    }

    /**
     * Set meta keywords
     * 
     * @param string $metaKeywords 
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = array();
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * Add meta keywords
     * 
     * @param string $metaKeywords
     * @param boolean $append TRUE adds the keyword to the last of the array|FALSE adds the keyword to the start of the array
     */
    public function addMetaKeywords($metaKeywords, $append=TRUE) {
        if ($append) {
            array_push($this->metaKeywords, $metaKeywords);
        } else {
            array_unshift($this->metaKeywords, $metaKeywords);
        }
    }

    /**
     * Get meta description
     * 
     * @return string
     */
    public function getMetaDescription() {
        if (is_array($this->metaDescription)) {
            return implode(', ', $this->metaDescription);
        }
        return $this->metaDescription;
    }

    /**
     * Set meta description
     * 
     * @param string $metaDescription 
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = array();
        $this->metaDescription = $metaDescription;
    }

    /**
     * Add meta description
     * 
     * @param string $metaDescription
     * @param boolean $append TRUE adds the description to the last of the array|FALSE adds the description to the start of the array
     */
    public function addMetaDescription($metaDescription, $append=TRUE) {
        if ($append) {
            array_push($this->metaDescription, $metaDescription);
        } else {
            array_unshift($this->metaDescription, $metaDescription);
        }
    }

}