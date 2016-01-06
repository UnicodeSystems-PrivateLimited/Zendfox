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
 * Class Admin_BackupController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Admin_BackupController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('backup/admin/backup');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Backups');
        $this->renderLayout();
    }

    /**
     * Create action
     */
    function createAction() {
        $backupModel = Fox::getModel('backup/backup');
        $backupModel->createBackup();
        $this->sendRedirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            try {
                $filePath = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . Fox_Backup_Model_Backup::BACKUP_PATH . DIRECTORY_SEPARATOR;
                $model = Fox::getModel('backup/backup');
                $model->load($id);
                if ($model->getId()) {
                    if ($model->getFileName() != '' && file_exists($filePath . $model->getFileName())) {
                        @unlink($filePath . $model->getFileName());
                    }
                    $model->delete();
                    Fox::getHelper('core/message')->setInfo('"' . $model->getFileName() . '" was successfully deleted.');
                }
            } catch (Exception $e) {
                Fox::getHelper('core/message')->setError($e->getMessage());
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('backup/admin/backup/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

    /**
     * Download action
     */
    public function downloadAction() {
        if (($id = $this->getRequest()->getParam('id'))) {
            $model = Fox::getModel('backup/backup');
            $filePath = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . Fox_Backup_Model_Backup::BACKUP_PATH . DIRECTORY_SEPARATOR;
            $model->load($id);
            if ($model->getId()) {
                $filename = $filePath . $model->getFileName();
                if ($model->getFileName() != '' && file_exists($filename)) {
                    header('Cache-Control: private; must-revalidate');
                    header('Pragma: no-cache');
                    header("Content-Type: application/zip");
                    header('Content-Length: ' . filesize($filename));
                    header('Content-Disposition: attachement; filename="' . $model->getFileName() . '"');
                    header('Content-Transfer-Encoding: binary');
                    readfile($filename);
                    return;
                }
            }
        }
        $this->sendRedirect('*/*/');
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('backup/backup');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            $filePath = Fox::getUploadDirectoryPath() . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . Fox_Backup_Model_Backup::BACKUP_PATH . DIRECTORY_SEPARATOR;
            if ($totalIds) {
                foreach ($ids as $d) {
                    $model->load($d);
                    @unlink($filePath . $model->getFileName());
                    $model->unSetData();
                }
                $model->delete($model->getPrimaryField() . ' IN (' . implode(',', $ids) . ')');
            }
            Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' record(s) successfully deleted.');
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}