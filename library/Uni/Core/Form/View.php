<?php

/**]
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
 * Class Uni_Core_Form_View
 * 
 * @uses        Uni_Core_View
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Form_View extends Uni_Core_View {

    /**
     * Sort order
     * 
     * @var int
     */
    protected $order;

    /**
     * Retrieve sort order
     * 
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Set sort order
     * 
     * @param int $order 
     * @return void
     */
    public function setOrder($order) {
        $this->order = $order;
    }

}