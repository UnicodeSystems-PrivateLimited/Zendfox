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
 * Class Newsletter_Admin_SubscriberController
 * 
 * @uses Fox_Admin_Controller_Action
 * @category    Fox
 * @package     Fox_Newsletter
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Newsletter_Admin_SubscriberController extends Fox_Admin_Controller_Action {

    /**
     * Index action
     */
    function indexAction() {
        $this->loadLayout();
        $view = Fox::getView('newsletter/admin/subscriber');
        if ($view) {
            $this->_addContent($view);
        }
        $view->setHeaderText('Newsletter Subscribers');
        $this->renderLayout();
    }

    /**
     * Table data action
     */
    public function ajaxTableAction() {
        $view = Fox::getView('newsletter/admin/subscriber/table');
        if ($view) {
            $content = $view->renderView();
            echo $content;
        }
    }

    /**
     * Export action
     * 
     * @return void
     */
    public function exportAction() {
        $view = Fox::getView('newsletter/admin/subscriber/table');
        if ($view) {
            $view->setDefaultRecordCount(0);
            $content = $view->renderView();
        }
        $type = $this->getRequest()->getParam('exportType');
        if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_CSV) {
            $view->exportToCSV('subscribers.csv');
        } else if ($type == Fox_Core_View_Admin_Table::EXPORT_TYPE_XML) {
            $view->exportToXML('subscribers.xml');
        }
        return;
    }

    /**
     * Bulk delete action
     */
    public function groupDeleteAction() {
        try {
            $model = Fox::getModel('newsletter/subscriber');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            if ($totalIds) {
                $model->delete($model->getPrimaryField() . ' IN (' . implode(',', $ids) . ')');
            }
            Fox::getHelper('core/message')->setInfo('Total ' . $totalIds . ' record(s) successfully deleted.');
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

    /**
     * Bulk unsubscribe action
     */
    public function groupUnsubscribeAction() {
        try {
            $model = Fox::getModel('newsletter/subscriber');
            $ids = $this->getGroupActionIds($model);
            $totalIds = count($ids);
            $count = 0;
            if ($totalIds) {
                for ($i = 0; $i < $totalIds; $i++) {
                    $model->load($ids[$i]);
                    if ($model->getId() && $model->unsubscribe()) {
                        $count++;
                    }
                    $model->unsetData();
                }
                Fox::getHelper('core/message')->setInfo('Total ' . $count . ' record(s) successfully unsubscribed.');
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
        echo Zend_Json_Encoder::encode(array('redirect' => Fox::getUrl('*/*/')));
    }

}