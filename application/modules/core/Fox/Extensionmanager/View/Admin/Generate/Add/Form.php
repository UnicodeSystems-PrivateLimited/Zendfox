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
 * Class Fox_Extensionmanager_View_Admin_Generate_Add_Form
 * 
 * @uses Fox_Core_View_Admin_Form
 * @category    Fox
 * @package     Fox_Extensionmanager
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Extensionmanager_View_Admin_Generate_Add_Form extends Fox_Core_View_Admin_Form {

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setModelKey('extensionmanager/generate/package');
        $this->setId("extension_generator");
    }

    /**
     * Prepare form
     * 
     * @param Uni_Core_Form $form 
     */
    protected function _prepareForm(Uni_Core_Form $form) {
        $form->setName('generate_package')->setMethod('post');
        $form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $subForm1 = new Uni_Core_Form_SubForm();
        $subForm1->setLegend('General Information');
        $subForm1->setDescription('General Information');
        
        $id = new Zend_Form_Element_Hidden('id');
        $name = new Zend_Form_Element_Text('name', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $name->setRequired(true)
                ->setLabel('Package Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $license = new Zend_Form_Element_Text('license', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $license->setRequired(true)
                ->setLabel('License')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $licenseUrl = new Zend_Form_Element_Text('license_url', array('size' => '30', 'maxlength' => 200, 'class' => 'url'));
        $licenseUrl->setLabel('License Url')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $description = new Zend_Form_Element_Textarea('description', array('cols' => '30', 'rows' => '5', 'class' => 'required'));
        $description->setRequired(true)
                ->setLabel('Description')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $summary = new Zend_Form_Element_Textarea('summary', array('cols' => '30', 'rows' => '3', 'class' => 'required'));
        $summary->setRequired(true)
                ->setLabel('Summary')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        
        $subForm1->addElements(array(
                    $id,
                    $name,
                    $description, 
                    $summary, 
                    $license, 
                    $licenseUrl));
        
        $subForm2 = new Uni_Core_Form_SubForm();
        $subForm2->setLegend('Release');
        $subForm2->setDescription('Release Information');
        
        $version = new Zend_Form_Element_Text('version', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $version->setRequired(true)
                ->setLabel('Version')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty')
                ->setDescription("Must be x.x.x.x");
        $stability = new Zend_Form_Element_Select('stability', array('class' => 'required'));
        $stability->setRequired(true)
                ->setLabel('Stability')
                ->setMultiOptions(Fox::getModel('extensionmanager/package/state')->_getOptionArray());
        $releaseNote = new Zend_Form_Element_Textarea('release_note', array('cols' => '30', 'rows' => '5', 'class' => 'required'));
        $releaseNote->setRequired(true)
                ->setLabel('Note')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $subForm2->addElements(array($version, $stability, $releaseNote));
        
        $subForm3 = new Uni_Core_Form_SubForm();
        $subForm3->setLegend('Contents');
        $subForm3->setDescription('Contents');
        $contentsView = Fox::getView('extensionmanager/admin/generate/add/contents');
        $contentsView->setId("package-contents");
        $subForm3->addSubView($contentsView);
        /*
        $subForm4 = new Uni_Core_Form_SubForm();
        $subForm4->setLegend('Dependencies');
        $subForm4->setDescription('Dependencies');
        
        $minPhpVersion = new Zend_Form_Element_Text('min_php_version', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $minPhpVersion->setRequired(true)
                ->setLabel('Min Php Version')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $maxPhpVersion = new Zend_Form_Element_Text('max_php_version', array('size' => '30', 'maxlength' => 200, 'class' => 'required'));
        $maxPhpVersion->setRequired(true)
                ->setLabel('Max Php Version')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
         
         $subForm4->addElements(array($minPhpVersion, $maxPhpVersion));
         */
        
        $subForm5 = new Uni_Core_Form_SubForm();
        $subForm5->setLegend('Provider Information');
        $subForm5->setDescription('Provider Information');
        
        $providerView = Fox::getView('extensionmanager/admin/generate/add/providers');
        $providerView->setId("package-provider"); 
        $subForm5->addSubView($providerView);
        
        $providerNote = new Zend_Form_Element_Textarea('provider_note', array('cols' => '30', 'rows' => '5'));
        $providerNote->setLabel('Note')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $subForm5->addElements(array($providerNote));
        $form->addSubForms(array(
            'general'=>$subForm1, 
            'release'=>$subForm2, 
            'provider'=>$subForm5,
            /*'dependencies'=> $subForm4,*/
            'content'=>$subForm3,
            ));
        if($formData = Fox::getModel('extensionmanager/session')->getFormData()){
            $form->setFormData($formData);
        }
        parent::_prepareForm($form);
    }

}

