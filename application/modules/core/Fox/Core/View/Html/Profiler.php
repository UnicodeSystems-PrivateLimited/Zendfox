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
 * Class Fox_Core_View_Html_Profiler
 * 
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Html_Profiler extends Fox_Core_View_Template {

    /**
     * Db profiler
     * 
     * @var Zend_Db_Profiler_Firebug
     */
    protected $profiler;

    /**
     * Returns whether the profiler is enabled
     * 
     * @return boolean
     */
    public function isProfilerEnabled() {
        return Fox::isProfilerEnabled();
    }

    /**
     * Get db profiler object
     * 
     * @return Zend_Db_Profiler_Firebug 
     */
    public function getProfiler() {
        if (NULL == $this->profiler) {
            $this->profiler = Uni_Fox::getDatabaseAdapter()->getProfiler();
        }
        return $this->profiler;
    }

}