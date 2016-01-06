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
 * Class Uni_Controller_Action
 * 
 * @uses        Zend_Controller_Action
 * @category    Uni
 * @package     Uni_Controller
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 
 *    
 */
class Uni_Controller_Action extends Zend_Controller_Action {
    
    /**#@+
     * Application modes constants
     */
    const MODE_WEB='web';
    const MODE_ADMIN='admin';
    const MODE_INSTALL='installer';
    /**#@-*/

    /**
     * @var Uni_Core_LayoutManager
     */
    private $layout;

    /**
     * Application Mode
     *
     * @var string
     */
    protected $appMode = 'web';

    /**
     * Initialize object 
     * 
     * Called from {@link __construct()} as final step of object instantiation.
     * 
     * @return void 
     */
    public function init() {
        parent::init();
        Fox::setMode($this->appMode);
        $this->_helper->urlScheme();        
    }

    /**
     * Loads layout for current module, controller, action.
     * 
     * @param string $layoutUpdate
     * @param string $updateKey
     * 
     * @return Uni_Core_LayoutManager
     */
    public function loadLayout($layoutUpdate=NULL, $updateKey=NULL) {
        if (NULL == $this->layout) {
            $request = $this->getRequest();
            $package = 'core';
            $theme = 'default';
            if ($this->appMode != self::MODE_INSTALL) {
                $package = Fox::getPreference($this->appMode . '/design/package');
                $theme = Fox::getPreference($this->appMode . '/design/theme');
            }
            $this->layout = new Uni_Core_LayoutManager($this->getFrontController()->getBaseUrl(), $request->getModuleName(), $request->getControllerName(), $request->getActionName(), $this->appMode, $package, $theme);
            $this->layout->load($layoutUpdate, $updateKey);
        }
        return $this->layout;
    }

    /**
     * Renders layout for current module, controller, action
     * 
     * @return void
     */
    public function renderLayout() {
        $this->layout->renderAll();
        $this->layout->echoContent();
    }

    /**
     * Retrieves view object from object key
     * 
     * @param string $key specified for any view object
     * 
     * @return Uni_Core_View|FALSE
     */
    public function getViewByKey($key) {
        if (is_array($this->layout->viewList) && isset($this->layout->viewList[$key])) {
            return $this->layout->viewList[$key];
        } else {
            return FALSE;
        }
    }

    /**
     * Retrieves all view objects
     * 
     * @return array associative array of key=>view object
     */
    public function getAllLoadedViews() {
        return $this->layout->viewList;
    }

    /**
     * Retrieves all keys of loaded layout view objects
     * 
     * @return array
     */
    public function getAllLoadedViewKeys() {
        return array_keys($this->layout->viewList);
    }

    /**
     * Alias to Fox::getUrl()
     * 
     * @param string $rout
     * @param array $params
     * @param boolean $reset
     * @return string
     */
    public function getUrl($rout='*/*/*', array $params=array(), $reset=TRUE) {
        return Fox::getUrl($rout, $params, $reset);
    }

    /**
     * 
     * @param string $rout
     * @param array $params
     * @param boolean $reset
     * @return void
     */
    public function sendRedirect($rout='*/*/*', array $params=array(), $reset=TRUE) {
        $urlParts = explode('/', $rout);
        $oldParams = $this->getRequest()->getParams();
        $module = (isset($urlParts[0]) ? (($urlParts[0] == '*') ? $oldParams['module'] : $urlParts[0]) : NULL);
        if(strtolower($module)=='admin'){
            $module=Fox::getAdminKey();
        }
        $controller = (isset($urlParts[1]) ? (($urlParts[1] == '*') ? $oldParams['controller'] : $urlParts[1]) : NULL);
        $action = (isset($urlParts[2]) ? (($urlParts[2] == '*') ? $oldParams['action'] : $urlParts[2]) : NULL);
        for ($i = 3; $i < count($urlParts); $i+=2) {
            $params[$urlParts[$i]] = (isset($urlParts[$i + 1]) ? $urlParts[$i + 1] : '');
        }
        if (!$reset) {
            unset($oldParams['module']);
            unset($oldParams['controller']);
            unset($oldParams['action']);
            $params = array_merge($oldParams, $params);
        }
        $this->_helper->redirector($action, $controller, $module, $params);
    }

    /**
     * Forward to another controller/action.
     *
     * It is important to supply the unformatted names, i.e. "article"
     * rather than "ArticleController".  The dispatcher will do the
     * appropriate formatting when the request is received.
     * 
     * @param string $rout routing path in format of "module/controller/action"
     * if "*" provided in place of module, controller or action
     * then current module, controller or action will used to forward
     * @param array $params
     * @return void
     */
    protected function sendForward($rout='*/*/*', array $params=array()) {
        $request = $this->getRequest();
        $routInfo['admin'] = Fox::getAdminKey();
        $urlParts = explode('/', $rout);
        $module = (isset($urlParts[0]) && ($urlParts[0] != '*')) ? $urlParts[0] : NULL;
        $controller = (isset($urlParts[1]) && ($urlParts[1] != '*')) ? $urlParts[1] : NULL;
        $action = (isset($urlParts[2]) && ($urlParts[2] != '*')) ? $urlParts[2] : NULL;
        if ($module == 'admin') {
            $module = $routInfo['admin'];
        }
        if (!empty($params)) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (null !== $module) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
                ->setDispatched(false);
    }

    /**
     * Redirects to the referer found by $_SERVER['HTTP_REFERER'] only if it is not equals to NULL, blank 
     * or $_SERVER['REQUEST_URI']           
     * 
     * @return void
     */
    protected function redirectReferer() {
        $referer = $this->getRequest()->getHeader('referer');
        if (!empty($referer) && $referer != $this->getRequest()->getRequestUri()) {
            $this->_helper->redirector->gotoUrl($this->getRequest()->getHeader('referer'));
        }else{
            $this->sendRedirect('');
        }
    }

    /**
     * Retrieves request uri after base url
     *
     * @return string 
     */
    public function getRequestUriAfterBaseUrl($uri=NULL,$queryString=TRUE) {
        $request = $this->getRequest();
        if ($uri == NULL) {
            $uri = $request->getRequestUri();
        }
        $baseUrl = $request->getBaseUrl('');
        if (trim($baseUrl) && ($pos = strpos($uri, $baseUrl)) !== FALSE) {
            if($queryString){
                $uri = substr($uri, $pos + strlen($baseUrl));                
            }else if(($qp=strpos($uri, '?'))!==FALSE){                  
                $length=$qp-($pos+strlen($baseUrl));                
                $uri = substr($uri,$pos+strlen($baseUrl),$length);
            }else{
                $uri = substr($uri, $pos + strlen($baseUrl));                
            }
        }
        return ltrim($uri, '/');
    }
    
    /**
     * Get logger object, alias to Fox::getLog()
     */
    public function getLog(){
        return Fox::getLog();
    }    

    /**
     * php magic method
     *
     * @param string $methodName
     * @param array $args 
     * @return void
     */
    public function __call($methodName, $args) {
        $this->sendForward('page-not-found');
    }

}