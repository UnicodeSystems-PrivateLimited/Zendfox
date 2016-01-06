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
 * Class Fox_Admin_View_Dashboard
 *
 * @uses Uni_Core_Admin_View
 * @category    Fox
 * @package     Fox_Admin
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Admin_View_Dashboard extends Uni_Core_Admin_View {

    /**
     *
     * @var Zend_Gdata_Analytics 
     */
    protected $_analytics;

    /**
     * Fetches and writes analytics data to cache and database
     */
    public function saveAnalyticsData() {
        try {
            if (!$this->isTodaysFileExists()) {
                $dayTimeStamp = 24 * 60 * 60;
                $prevDateTimeStamp = time();
                $dataArray = array();
                $fromDate = date('Y-m-d', ($prevDateTimeStamp - (30 * $dayTimeStamp)));
                $toDate = date('Y-m-d', ($prevDateTimeStamp - $dayTimeStamp));
                $analyticsData = Fox::getModel('admin/analytics')->getAnalyticsData($fromDate, $toDate);
                $model = Fox::getModel('admin/analytics');
                $ga_profile_id = $this->getProfileId();
                for ($i = 30; $i >= 1; $i--) {
                    $date = date('Y-m-d', ($prevDateTimeStamp - ($i * $dayTimeStamp)));
                    $value = Fox_Core_Model_Date::__toLocaleDate(strtotime($date), "d-M-Y");
                    $total_results = 0;
                    $page_views = 0;
                    $visits = 0;
                    if (false && array_key_exists($date, $analyticsData)) {
                        $total_results = $analyticsData[$date]['page_views'];
                        $page_views = $analyticsData[$date]['total_results'];
                        $visits = $analyticsData[$date]['total_visits'];
                    } else {
                        $results = $this->requestReportData($ga_profile_id, $date, $date);
                        if (count($results) > 0) {
                            $page_views = $results['page_views'];
                            $visits = $results['visits'];
                        }
                        $model->setDateValue($date);
                        $model->setTotalResults($total_results);
                        $model->setPageViews($page_views);
                        $model->setTotalVisits($visits);
                        $model->save();
                        $model->unsetData();
                    }
                    $dataArray[$i]['value'] = $value;
                    $dataArray[$i]['page_views'] = $page_views;
                    $dataArray[$i]['total_visits'] = $visits;
                }
                $this->writeData($dataArray);
            }
        } catch (Exception $e) {
            Fox::getHelper('core/message')->setError($e->getMessage());
        }
    }

    /**
     * Returns profile id for website
     * 
     * @return string|FALSE
     */
    public function getProfileId() {
        $profileId = FALSE;
        $accountId = FALSE;
        $gaAccountName = Fox::getPreference('admin/analytics/ga_account_name');
        $gaEmail = Fox::getPreference('admin/analytics/ga_email');
        $gaPassword = Fox::getPreference('admin/analytics/ga_password');
        $service = Zend_Gdata_Analytics::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($gaEmail, $gaPassword, $service);
        $this->_analytics = new Zend_Gdata_Analytics($client);
        $accounts = $this->_analytics->getAccountFeed();
        foreach ($accounts as $account) {
            if ($account->accountName == $gaAccountName) {
                $accountId = $account->accountId;
            }
        }
        if (!$accountId) {
            throw new Exception('Google Ananlytics Account with name "' . $gaAccountName . '" does not exist.');
        }
        $profileResult = $this->_analytics->getDataFeed($this->_analytics->newAccountQuery()->profiles('~all', $accountId));
        foreach ($profileResult as $dataBit) {
            foreach ($dataBit->getExtensionElements() as $element) {
                $attributes = $element->getExtensionAttributes();
                if ($attributes['name']['value'] == 'ga:profileId') {
                    $profileId = $attributes['value']['value'];
                    break;
                }
            }
        }
        if (!$profileId) {
            throw new Exception('Google Ananlytics Profile not found for "' . $gaAccountName . '".');
        }
        return $profileId;
    }

    /**
     * Writes xml data to cache file
     *
     * @param array $dataArray
     */
    public function writeData($dataArray) {
        $filePath = $this->getReportsFilePath();
        $xDom = new Uni_Data_XDOMDocument();
        $xDom->preserveWhiteSpace = FALSE;
        $xDom->formatOutput = TRUE;
        $root = $xDom->createElement('root');
        foreach ($dataArray as $key => $value) {
            $info = $xDom->createElement('record');
            foreach ($value as $name => $values) {
                if ($name == 'value') {
                    $info->setAttribute('date', $values);
                } else {
                    $detail = $xDom->createElement($name, $values);
                    $info->appendChild($detail);
                }
            }
            $root->appendChild($info);
        }
        $xDom->appendChild($root);
        $xDom->save($filePath);
    }

    /**
     * Returns path of xml file
     * 
     * @return string
     */
    public function getReportsFilePath() {
        $directoryPath = CACHE_DIR . DIRECTORY_SEPARATOR . 'analytics';
        if (!file_exists($directoryPath)) {
            @mkdir($directoryPath, 0777);
        }
        $filePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . Fox_Core_Model_Date::getCurrentDate('Y-m-d') . '.xml';
        return $filePath;
    }

    /**
     * Retrieves whether the file exists for today
     *
     * @return boolean
     */
    public function isTodaysFileExists() {
        return file_exists($this->getReportsFilePath());
    }

    /**
     * Retrieves whether the chart is enabled
     *
     * @return boolean
     */
    public function isChartEnabled() {
        return Fox::getPreference('admin/analytics/enable_charts');
    }

    /**
     * Returns url of google analytics setting
     *
     * @return string
     */
    public function getAnalyticsSettingUrl() {
        return Fox::getUrl('admin/system_setting/edit', array('item' => 'admin', 'lbl' => 'Admin'));
    }

    /**
     * Fetches data from google analytics
     * 
     * @param string $profileId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function requestReportData($profileId, $startDate, $endDate) {
        $data = array();
        $query = $this->_analytics->newDataQuery();
        $query->setProfileId($profileId)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_PAGEVIEWS)
                ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setMaxResults(50);
        $results = $this->_analytics->getDataFeed($query);
        foreach ($results as $row) {
            $data['visits'] = $row->getMetric('ga:visits');
            $data['page_views'] = $row->getMetric('ga:pageviews');
        }
        return $data;
    }

}