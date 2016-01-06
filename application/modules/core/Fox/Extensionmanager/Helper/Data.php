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
 * Class Fox_Extensionmanager_Helper_Data
 * 
 * @uses Uni_Core_Helper
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_Helper_Data extends Uni_Core_Helper {

    /**
     * Contains a key for encrypt/decrypt
     * 
     * @var string 
     */
    private $secretKey = "/*x*&(z&xz(*d&()c*@fn";

    /**
     * Encrypt/Decrypt operation on given string using key
     * 
     * @param string $str target string
     * @param string $action Operation to perform
     * 
     * @return string resulting string after encrypt/decrypt operation
     * @access protected
     */
    protected function encryptDecrypt($str, $action = 'encrypt') {
        $key = $this->secretKey;
        $res = '';
        if ($action !== 'encrypt') {
            $str = base64_decode($str);
        }
        for ($i = 0; $i < strlen($str); $i++) {
            $c = ord(substr($str, $i));
            if ($action == 'encrypt') {
                $c += ord(substr($key, (($i + 1) % strlen($key))));
                $res .= chr($c & 0xFF);
            } else {
                $c -= ord(substr($key, (($i + 1) % strlen($key))));
                $res .= chr(abs($c) & 0xFF);
            }
        }
        if ($action == 'encrypt') {
            $res = base64_encode($res);
        }
        return $res;
    }

    /**
     * Performs encryption on given string
     * 
     * @param string $str target string
     * @return string resulting string 
     * @access public
     */
    public function encrypt($str) {
        return $this->encryptDecrypt($str, "encrypt");
    }

    /**
     * Performs decryption on given string
     * 
     * @param string $str target string
     * @return string resulting string
     * @access public
     */
    public function decrypt($str) {
        return $this->encryptDecrypt($str, "decrypt");
    }

    /**
     * Validates version to have strict pattern like x.x.x.x
     * 
     * @param string $version
     * @return bool
     * @access public
     */
    public function validateVersion($version) {
        $n = explode('.', $version);
        if (preg_match("/" . '(\\d+)' . '(.)' . '(\\d+)' . '(.)' . '(\\d+)' . '(.)' . '(\\d+)' . "$/", $version) && count($n) == 4) {
            return TRUE;
        }
        return FALSE;
    }

}