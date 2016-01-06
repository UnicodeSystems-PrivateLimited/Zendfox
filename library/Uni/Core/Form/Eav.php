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
 * Class Uni_Core_Form_Eav
 * 
 * @uses        Uni_Core_Form
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_Form_Eav extends Uni_Core_Form {

    /**
     *
     * @var array 
     */
    protected $eavElements = array();

    /**
     * Autometicaly creates form and form elements for eav type models
     * 
     * @param array $eavMetaData 
     * @return void
     */
    function createEavForm(array $eavMetaData) {        
        $this->setEnctype('multipart/form-data');
        foreach ($eavMetaData as $k => $eavGroup) {
            $subForm = new Zend_Form_SubForm(array(
                        'legend' => $k
                    ));
            foreach ($eavGroup as $eavEle) {
                $ele = $this->createElement($eavEle['input_type'], $eavEle['attribute_code'], array(
                            'label' => $eavEle['frontend_label']
                        ));
                if (!empty($eavEle['is_required'])) {
                    $class = 'required';
                    $ele->setRequired(TRUE);
                }
                $class = (isset($class) ? "$class " : '') . (!empty($eavEle['validation']) ? str_replace(',', ' ', $eavEle['validation']) : '');
                $ele->setAttrib('class', $class);
                switch ($eavEle['input_type']) {
                    case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_SELECT:
                    case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                    case Fox_Eav_Model_Attribute::ATTRIBUTE_TYPE_RADIO:
                        if (method_exists($ele, 'setMultiOptions')) {
                            $ele->setMultiOptions($eavEle['multiOptions']);
                        }
                }
                if (isset($eavEle['default_value'])) {
                    $ele->setValue($eavEle['default_value']);
                }
                if (isset($eavEle['belongsTo'])) {
                    $ele->setBelongsTo($eavEle['belongsTo']);
                }
                if (isset($eavEle['description'])) {
                    $ele->setDescription($eavEle['description']);
                }
                $subForm->addElement($ele);
                $this->eavElements[$eavEle['attribute_code']] = $ele;
                unset($class);
            }
            $this->addSubForm($subForm, $k);
        }
    }

    /**
     * Retrievs form elements added at specified key
     *
     * @param string $key
     * @return Zend_Form_Element 
     */
    public function getEavElement($key) {
        return (isset($this->eavElements[$key]) ? $this->eavElements[$key] : NULL);
    }

}