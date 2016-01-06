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
 * Class Fox_Cms_View_Block
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Cms
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Cms_View_Block extends Fox_Core_View_Template {

    /**
     * Block content
     * 
     * @var string
     */
    private $blockContent = '';
    /**
     * Block parsed content
     * 
     * @var string
     */
    private $parsedContent;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('cms/block');
    }

    /**
     * Get parsed block content
     * 
     * @return string
     */
    public function getBlockContent() {
        if (NULL == $this->parsedContent) {
            $model = Fox::getModel('cms/block');
            $model->load($this->getViewKey(), 'identifier_key');
            if ($model->getId() && $model->getStatus() == Fox_Cms_Model_Block::STATUS_ENABLED) {
                $this->blockContent = $model->getContent();
                $this->parsedContent = stripslashes($this->getParsedContent($this->blockContent));
            }
        }
        return (($this->parsedContent) ? $this->parsedContent : '');
    }

    /**
     * Set block content
     * 
     * @param string $blockContent 
     */
    public function setBlockContent($blockContent) {
        $this->blockContent = $blockContent;
    }

}