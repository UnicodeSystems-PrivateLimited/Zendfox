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
 * Class Uni_View_Helper_GetBlockContent
 * Helper class to fetch cms block content
 * 
 * @uses        Zend_View_Helper_Abstract
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_View_Helper_GetBlockContent extends Zend_View_Helper_Abstract {

    /**
     * Get parsed cms block content
     *
     * @param string $identifierKey
     * @return string 
     */
    public function getBlockContent($identifierKey) {
        $parsedContent=NULL;
            $model = Fox::getModel('cms/block');
            $model->load($identifierKey, 'identifier_key');
            if ($model->getId() && $model->getStatus() == Fox_Cms_Model_Block::STATUS_ENABLED) {
                $parsedContent= $this->view->getParsedContent($model->getContent());
            }
        return $parsedContent;
       
    }

}