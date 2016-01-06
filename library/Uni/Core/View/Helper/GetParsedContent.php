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
 * Class Uni_View_Helper_GetParsedContent
 * Helper class to parse editor contents to generate final html contents
 * with replacement of special tokens from there actual values
 * 
 * @uses        Zend_View_Helper_Abstract
 * @category    Uni
 * @package     Uni_View
 * @subpackage  Uni_View_Helper
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_View_Helper_GetParsedContent extends Zend_View_Helper_Abstract {

    /**
     * Method for helper calling
     *
     * @param string $content
     * @return string 
     */
    public function getParsedContent($content) {
        $pat = '/{{([^{}]+)}}/';
        $replaceWith = '{$this->replaceMatched}';
        $content = addslashes($content);
        $content = preg_replace_callback($pat, 'Uni_View_Helper_GetParsedContent::replaceMatched', $content);
        eval('$parsed="' . $content . '";');
        return isset($parsed) ? $parsed : stripslashes($content);
    }

    /**
     * Callback for preg_replace_callback
     *
     * @param array $matches
     * @return string 
     */
    public static function replaceMatched($matches) {
        $content = '';
        if (strpos($matches[1], 'view') === 0) {
            $viewCont = explode(' ', $matches[1]);
            $class = 'NULL';
            $key = 'NULL';
            $tpl = 'NULL';
            foreach ($viewCont as $vc) {
                $attr = explode('=', $vc);
                if (count($attr) > 1) {
                    switch (strtolower($attr[0])) {
                        case 'class': $class = $attr[1];
                            break;
                        case 'key': $key = $attr[1];
                            break;
                        case 'tpl': $tpl = $attr[1];
                            break;
                    }
                }
            }            
            $content = stripslashes("{\$this->view->getViewContent($class,$key,$tpl)}");
        } else {
            $content = '{$this->view->' . stripslashes($matches[1]) . '}';
        }
        return $content;
    }

}