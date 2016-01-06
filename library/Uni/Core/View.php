<?php

/**
 * @see Zend_view
 */
require_once 'Zend/View.php';

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
 * Class Uni_Core_View
 * Base class of zendfox view system
 * uses to render layout to provide view content.
 * 
 * @uses        Zend_View
 * @category    Uni
 * @package     Uni_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Core_View extends Zend_View {
    /**#@+
     * Template file extension constant
     */
    const VIEW_SUFFIX='.phtml';
    /**#@-*/

    /**
     * Whether the view has been rendered or not
     *
     * @var boolean 
     */
    public $rendered = FALSE;
    
    /**
     *
     * @var string 
     */
    protected $viewBase;
    
    /**
     *
     * @var string 
     */
    protected $defaultScriptPath;
    
    /**
     *
     * @var int 
     */
    protected $auto;
    
    /**
     *
     * @var string 
     */
    private $template = '';
    
    /**
     *
     * @var string 
     */
    private $content;
    
    /**
     *
     * @var array 
     */
    private $views = array();
    
    /**
     *
     * @var string 
     */
    protected $viewKey;
    
    /**
     *
     * @var array 
     */
    protected $viewOptions = array();

    /**
     * Constructs new object using template name and auto render params
     *
     * @param string $template
     * @param int $auto 
     */
    function __construct($template='', $auto=FALSE) {
        parent::__construct();
        $this->addHelperPath('Uni/Core/View/Helper', 'Uni_View_Helper');
        $this->viewBase = APPLICATION_PATH . DS . 'views' . DS . Fox::getMode() . DS;
        $this->defaultScriptPath = $this->viewBase . 'core' . DS . 'default' . DS . 'template';
        $this->auto = $auto;
        if ($template != '') {
            $this->setTemplate($template);
        }
    }

    /**
     * Gets unique view identifier key
     *
     * @return string 
     */
    public function getViewKey() {
        return $this->viewKey;
    }

    /**
     * Sets unique key for this view
     *
     * @param string $viewKey 
     * @return void
     */
    public function setViewKey($viewKey=NULL) {
        $this->viewKey = $viewKey;
    }

    /**
     * @return array 
     */
    public function getViewOptions() {
        return $this->viewOptions;
    }

    /**
     *
     * @param array $viewOptions 
     * @return void
     */
    public function setViewOptions(array $viewOptions) {
        $this->viewOptions = $viewOptions;
    }

    /**
     * Instantiate view object of given class
     * and renderes html contents
     * 
     *
     * @param string $class
     * @param string $key
     * @param string $tpl
     * @param array $options
     * @return string 
     */
    public function getViewContent($class, $key=NULL, $tpl=NULL, array $options=array()) {
        $view = Fox::getView($class);
        if ($view) {
            if ($key) {
                $view->setViewKey($key);
            }
            if ($tpl) {
                $view->setTemplate($tpl);
            }
            if ($options) {
                $view->setViewOption($options);
            }
            return $view->renderView();
        }
        return '';
    }

    /**
     * Sets template for rendering of view
     *
     * @param string $template 
     * @return void
     */
    public function setTemplate($template) {
        $package = Fox::getPreference(Fox::getMode() . '/design/package');
        $theme = Fox::getPreference(Fox::getMode() . '/design/theme');
        $sPath = $this->viewBase . $package . DS . $theme . DS . 'template';
        if (!file_exists($sPath . DS . $template . self::VIEW_SUFFIX)) {
            $sPath = $this->defaultScriptPath;
        }
        parent::setScriptPath($sPath);
        $this->template = $template;
    }

    /**
     * Renderes contents of view object stored on given key
     *
     * @param type $key
     * @return string 
     */
    public function getContent($key) {
        if (array_key_exists($key, $this->views)) {
            return $this->views[$key]->renderView();
        }
        return '';
    }

    /**
     * Renderes view contents
     * 
     *
     * @return string 
     */
    public function renderView() {
        if (!$this->rendered) {
            if (isset($this->template) && $this->template != '') {
                $this->content = parent::render($this->_getTemplate());
                $this->rendered = TRUE;
            } else if ($this->auto) {
                foreach ($this->views as $k => $v) {
                    $this->content = $this->content . $v->renderView();
                }
            }
        }
        return $this->_getContent();
    }

    /**
     *
     * @param string $code 
     */
    public function addHead($code) {
        Fox::getHelper('core/head')->addHTML($code);
    }

    /**
     * Adds passed view object at specified kay as child view
     *
     * @param string $key
     * @param Uni_Core_View $view 
     */
    public function addChild($key, Uni_Core_View $view) {
        $this->views[$key] = $view;
    }

    /**
     * Gets child view object stored on specified key
     *
     * @param string $key
     * @return mixed 
     */
    public function getChild($key) {
        return (array_key_exists($key, $this->views) ? $this->views[$key] : FALSE);
    }

    /**
     * Compose path for theme files
     *
     * @param string $path
     * @return string 
     */
    public function themeUrl($path='') {
        return Fox::getThemeUrl($path);
    }

    /**
     * Retrieves current Request object
     *
     * @return Uni_Controller_Request_Http 
     */
    public function getRequest() {
        return Fox::getRequest();
    }

    /**
     * Composes complete url for given parameters
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
     * Compose full qualified template name
     *
     * @return string 
     */
    protected function _getTemplate() {
        return (($this->template != '') ? $this->template . self::VIEW_SUFFIX : '');
    }

    /**
     *
     * @return string 
     */
    protected function _getContent() {
        if (Fox::isEnableTemplateHint()) {
            $content = '<div class="tpl-hint"><span class="tpl-hint">' . get_class($this) . '</span><span class="tpl-hint">' . $this->_getTemplate() . '</span>' . $this->content . '</div>';
        } else {
            $content = $this->content;
        }
        return $content;
    }
    
    /**
     * Translates string into selected language from language files
     * returns same string if key not found.
     * 
     * @param string $str
     * @return string
     */
    public function _($str){
        return self::translate($str);
    }

}