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
 * Class Fox_Core_View_Admin_Form_Container
 * 
 * @uses Fox_Core_View_Admin_Container
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_View_Admin_Form_Container extends Fox_Core_View_Admin_Container {
    const FORM_KEY='__form__';
    /**
     * Back button text
     * 
     * @var string
     */
    protected $backButtonText = 'Back';
    /**
     * Reset button text
     * 
     * @var string
     */
    protected $resetButtonText = 'Reset';
    /**
     * Delete button text
     * 
     * @var string
     */
    protected $deleteButtonText = 'Delete';
    /**
     * Save button text
     * 
     * @var string
     */
    protected $saveButtonText = 'Save';
    /**
     * Save and edit button text
     * 
     * @var string
     */
    protected $saveAndEditButtonText = 'Save &amp; continue edit';
    /**
     * Delete button action
     * 
     * @var string
     */
    protected $deleteButtonAction = 'delete';
    /**
     * Delete button url
     * 
     * @var string
     */
    protected $deleteButtonUrl;
    /**
     * Delete confirmation text
     * 
     * @var string
     */
    protected $deleteConfirmText = 'Do you really want to delete ?';
    /**
     * Save button action
     * 
     * @var string
     */
    protected $saveButtonAction = 'save';
    /**
     * Save and edit button action
     * 
     * @var string
     */
    protected $saveAndEditButtonAction = 'save';
    /**
     * Used internally to show edit button
     * 
     * @var boolean
     */
    protected $editing = FALSE;
    /**
     * Save button visibility
     * 
     * @var boolean
     */
    protected $isSaveButtonEnabled = TRUE;
    /**
     * Edit button visibility
     * 
     * @var boolean
     */
    protected $isEditButtonEnabled = TRUE;
    /**
     * Delete button visibility
     * 
     * @var boolean
     */
    protected $isDeleteButtonEnabled = TRUE;
    /**
     * Back button visibility
     * 
     * @var boolean
     */
    protected $isBackButtonEnabled = TRUE;
    /**
     * Reset button visibility
     * 
     * @var boolean
     */
    protected $isResetButtonEnabled = TRUE;

    /**
     * Public constructor
     */
    function __construct() {
        parent::__construct();
        $this->setTemplate('fox/containers/form/container');
    }

    /**
     * Get back button text
     */
    public function getBackButtonText() {
        return $this->backButtonText;
    }

    /**
     * Set back button text
     * 
     * @param string $backButtonText
     */
    public function setBackButtonText($backButtonText) {
        $this->backButtonText = $backButtonText;
    }

    /**
     * Get reset button text
     * 
     * @return string
     */
    public function getResetButtonText() {
        return $this->resetButtonText;
    }

    /**
     * Set reset button text
     * 
     * @param string $resetButtonText
     */
    public function setResetButtonText($resetButtonText) {
        $this->resetButtonText = $resetButtonText;
    }

    /**
     * Get delete button text
     * 
     * @return string
     */
    public function getDeleteButtonText() {
        return $this->deleteButtonText;
    }

    /**
     * Set delete button text
     * 
     * @param string $deleteButtonText
     */
    public function setDeleteButtonText($deleteButtonText) {
        $this->deleteButtonText = $deleteButtonText;
    }

    /**
     * Get delete confirmation text
     * 
     * @return string
     */
    public function getDeleteConfirmText() {
        return $this->deleteConfirmText;
    }

    /**
     * Set delete confirmation text
     * 
     * @param string $deleteConfirmText
     */
    public function setDeleteConfirmText($deleteConfirmText) {
        $this->deleteConfirmText = $deleteConfirmText;
    }

    /**
     * Get save button text
     * 
     * @return string
     */
    public function getSaveButtonText() {
        return $this->saveButtonText;
    }

    /**
     * Set save button text
     * 
     * @param string $saveButtonText
     */
    public function setSaveButtonText($saveButtonText) {
        $this->saveButtonText = $saveButtonText;
    }

    /**
     * Get edit button text
     * 
     * @return string
     */
    public function getSaveAndEditButtonText() {
        return $this->saveAndEditButtonText;
    }

    /**
     * Set edit button text
     * 
     * @param string $saveAndEditButtonText
     */
    public function setSaveAndEditButtonText($saveAndEditButtonText) {
        $this->saveAndEditButtonText = $saveAndEditButtonText;
    }

    /**
     * Get delete button url
     */
    public function getDeleteButtonUrl() {
        return $this->deleteButtonUrl;
    }

    /**
     * Set delete button action
     * 
     * @param string $deleteButtonAction
     */
    public function setDeleteButtonAction($deleteButtonAction) {
        $this->deleteButtonAction = $deleteButtonAction;
    }

    /**
     * Get save button url
     * 
     * @return string
     */
    public function getSaveButtonUrl() {
        return Fox::getUrl('*/*/' . $this->saveButtonAction);
    }

    /**
     * Set save button action
     * 
     * @param string $saveButtonAction
     */
    public function setSaveButtonAction($saveButtonAction) {
        $this->saveButtonAction = $saveButtonAction;
    }

    /**
     * Get whether the form is in edit mode
     * 
     * @return boolean
     */
    public function isEditing() {
        return $this->editing;
    }

    /**
     * Set the form to be in save or edit mode
     * 
     * @param boolean $editing
     */
    public function setEditing($editing) {
        $this->editing = $editing;
    }

    /**
     * Get the visiblity of save button
     * 
     * @return boolean
     */
    public function getIsSaveButtonEnabled() {
        return $this->isSaveButtonEnabled;
    }

    /**
     * Set the visiblity of save button
     * 
     * @param boolean $isSaveButtonEnabled
     */
    public function setIsSaveButtonEnabled($isSaveButtonEnabled) {
        $this->isSaveButtonEnabled = $isSaveButtonEnabled;
    }

    /**
     * Get the visiblity of edit button
     * 
     * @return boolean
     */
    public function getIsEditButtonEnabled() {
        return $this->isEditButtonEnabled;
    }

    /**
     * Set the visiblity of edit button
     * 
     * @param boolean $isEditButtonEnabled
     */
    public function setIsEditButtonEnabled($isEditButtonEnabled) {
        $this->isEditButtonEnabled = $isEditButtonEnabled;
    }

    /**
     * Get the visiblity of save button
     * 
     * @return boolean
     */
    public function getIsDeleteButtonEnabled() {
        return $this->isDeleteButtonEnabled;
    }

    /**
     * Set the visiblity of delete button
     * 
     * @param boolean $isDeleteButtonEnabled
     */
    public function setIsDeleteButtonEnabled($isDeleteButtonEnabled) {
        $this->isDeleteButtonEnabled = $isDeleteButtonEnabled;
    }

    /**
     * Get the visiblity of back button
     * 
     * @return boolean
     */
    public function getIsBackButtonEnabled() {
        return $this->isBackButtonEnabled;
    }

    /**
     * Set the visiblity of back button
     * 
     * @param boolean $isBackButtonEnabled
     */
    public function setIsBackButtonEnabled($isBackButtonEnabled) {
        $this->isBackButtonEnabled = $isBackButtonEnabled;
    }

    /**
     * Get the visiblity of reset button
     * 
     * @return boolean
     */
    public function getIsResetButtonEnabled() {
        return $this->isResetButtonEnabled;
    }

    /**
     * Set the visiblity of reset button
     * 
     * @return boolean $isResetButtonEnabled
     */
    public function setIsResetButtonEnabled($isResetButtonEnabled) {
        $this->isResetButtonEnabled = $isResetButtonEnabled;
    }

    /**
     * Get form object
     * 
     * @return object
     */
    public function getForm() {
        if (($form = $this->getChild(self::FORM_KEY))) {
            return $form;
        } else {
            $formClass = get_class($this) . '_Form';
            $formView = Fox::getView($formClass);
            if ($formView) {
                $this->addChild(self::FORM_KEY, $formView);
                return $formView;
            }
        }
    }

    /**
     * Prepare container
     */
    protected function _prepareContainer() {
        if (NULL == $this->id) {
            $this->id = get_class($this);
        }
        $formView = $this->getForm();
        if (($model = Fox::getModel($formView->getModelKey())) && $model->getId()) {
            $this->editing = TRUE;
            $this->deleteButtonUrl = Fox::getUrl('*/*/' . $this->deleteButtonAction, array($model->getPrimaryKey() => $model->getId()));
        }
    }

    /**
     * Render contents
     * 
     * @return string
     */
    public function renderView() {
        return parent::renderView();
    }

}