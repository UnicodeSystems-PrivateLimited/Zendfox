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
 * Class Uni_Core_Session
 * manages session in application
 *  
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Session {
    
    /**#@+
     * method type constants
     */
    const GET='get';
    const SET='set';
    const CLEAR='unset';
    /**#@-*/
    
    /**
     *
     * @var Zend_Session_Namespacepe 
     */
    protected $session;

    /**
     * Gets object of Zend_Session_Namespace
     *
     * @return Zend_Session_Namespace
     */
    protected function getSession(){
        if(NULL== $this->session){
            $this->session=new Zend_Session_Namespace(get_class($this));
        }
        return $this->session;
    }
    
    /**
     * Trapping Call of undefined methods.
     *
     * @param string $method
     * @param string $params
     * @return mixed 
     */
    public function __call($method, $params) {
        $act = substr($method, 0, 3);
        $key = lcfirst(substr($method, 3));
        $session = $this->getSession();
        if ($act == self::GET) {
            return isset($session->$key) ? $session->$key : NULL;
        } else if ($act == self::SET) {
            $session->$key = $params[0];
        } else {
            $act = substr($method, 0, 5);
            $key = lcfirst(substr($method, 5));
            if ($act == self::CLEAR) {
                $session->__unset($key);
            }
        }
    }

    /**
     * Clears all session data
     * 
     * @return void
     */
    public function unsetAllData(){
        $session = $this->getSession();
        $session->unsetAll();
    }

}