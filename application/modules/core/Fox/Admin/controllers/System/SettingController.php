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
 * Class Admin_System_SettingController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_System_SettingController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->sendForward('*/*/edit');
    }

    /**
     * Edit action
     */
    function editAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $preferenceModel = Fox::getModel('core/preference');
            $preferenceModel->getAdapter()->beginTransaction();
            try {
                if (!empty($_FILES)) {
                    $path = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . Fox_Core_Model_Preference::CORE_UPLOAD_FOLDER;
                    if (!file_exists($path)) {
                        if (!@mkdir($path, 0777, TRUE)) {
                            throw new Exception('uploads directory was not found.');
                        }
                        @chmod($path, 0777);
                    }
                    foreach ($_FILES as $mainKey => $value) {
                        if (is_array($value) && (isset($value['name'])) || isset($value['tmp_name'])) {
                            $i = 0;
                            foreach ($value as $section => $field) {
                                foreach ($field as $key => $fieldValue) {
                                    if ($section == 'name') {
                                        foreach ($fieldValue as $fieldName => $fileField) {
                                            $fieldKey = $mainKey . '/' . $key . '/' . $fieldName;
                                            $preferenceModel->load($fieldKey, 'name');
                                            if (isset($_FILES[$mainKey]) && isset($_FILES[$mainKey]['name']) && isset($_FILES[$mainKey]['name'][$key]) && isset($_FILES[$mainKey]['name'][$key][$fieldName])) {
                                                $fileName = $_FILES[$mainKey]['name'][$key][$fieldName];
                                                if (($pos = strrpos($fileName, '.')) > -1) {
                                                    if (file_exists($path . DIRECTORY_SEPARATOR . $preferenceModel->getValue())) {
                                                        @unlink($path . DIRECTORY_SEPARATOR . $preferenceModel->getValue());
                                                    }
                                                    $ext = substr($fileName, ($pos + 1));
                                                    $fileName = 'FILE-' . time() . $i++ . '.' . $ext;
                                                    $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
                                                    move_uploaded_file($_FILES[$mainKey]['tmp_name'][$key][$fieldName], $filePath);
                                                    @chmod($filePath, 0777);
                                                    $preferenceModel->setName($fieldKey);
                                                    $preferenceModel->setValue($fileName);
                                                    $preferenceModel->save();
                                                    $preferenceModel->unsetData();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                foreach ($data as $mainKey => $value) {
                    if (is_array($value)) {
                        foreach ($value as $section => $field) {
                            foreach ($field as $key => $fieldValue) {
                                $fieldKey = $mainKey . '/' . $section . '/' . $key;
                                $preferenceModel->load($fieldKey, 'name');
                                $preferenceModel->setName($fieldKey);
                                if (is_array($fieldValue)) {
                                    $val = implode(',', $fieldValue);
                                } else {
                                    $val = $fieldValue;
                                }
                                $preferenceModel->setValue($val);
                                $preferenceModel->save();
                                $preferenceModel->unsetData();
                            }
                        }
                    }
                }
                $preferenceModel->getAdapter()->commit();
                Uni_Core_Preferences::loadPreferences(TRUE);
                Fox::initializePreferences();
                Fox::getHelper('core/message')->setInfo('Data was successfully saved.');
            } catch (Exception $e) {
                Fox::getModel('core/session')->setFormData($data);
                Fox::getHelper('core/message')->setError($e->getMessage());
                $preferenceModel->getAdapter()->rollback();
                $this->sendRedirect('*/*/edit', array('item' => $mainKey));
            }
        }
        if (isset($mainKey)) {
            $this->sendRedirect('*/*/edit', array('item' => $mainKey));
        } else {
            $this->sendRedirect('*/*/');
        }
    }

}