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
 * Class Fox_Member_View_Admin_Dashboard
 *
 * @uses Fox_Core_View_Template
 * @category    Fox
 * @package     Fox_Member
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Member_View_Admin_Dashboard extends Fox_Core_View_Template {
    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
    }
    /**
     * Get recent members
     *
     * @param int $limit
     * @return Uni_Core_Model_Collection
     */
    function getRecentMembers($limit=5){
        $model=Fox::getModel('member/member');
        $collection=$model->getCollection(NULL,'*','id DESC',$limit);        
        return $collection;
    }
}
