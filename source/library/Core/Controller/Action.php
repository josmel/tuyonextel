<?php

class Core_Controller_Action extends Zend_Controller_Action {

    /**
     *
     * @var Zend_Form_Element_Hash
     */
    protected $_hash = null;
    
    /**
     *
     * @var Zend_Translate
     */
    protected $_translate = null;
    
    /**
     *
     * @var Core_Controller_Action_Helper_FlashMessengerCustom
     */
    protected $_flashMessenger;

    protected $_config;
    
    protected $_yosonVars = array();
    
    public function init() {
        parent::init();
        // $this->_session = new Zend_Session_Namespace('sessionGeneral');
        //var_dump($this->getRequest()); exit;
        $this->_flashMessenger = new Core_Controller_Action_Helper_FlashMessengerCustom();        
        $this->_config = $this->getConfig();
        $this->_layout = Zend_Layout::getMvcInstance();
        //$this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * Pre-dispatch routines
     * Asignar variables de entorno
     *
     * @return void
     */
    public function preDispatch() {
        parent::preDispatch();
        $this->view->controller=$this->getRequest()->getControllerName(); 
        $this->view->action=$this->getRequest()->getActionName();
        $this->view->module=$this->getRequest()->getModuleName();
        
//        $config = $this->getConfig();
        
//        $this->config = $this->getConfig();
        //Zend_Debug::dump($this->config);exit;
        //$this->log = $this->getLog();
        
        //$this->cache = $this->getCache();
//        $this->siteUrl = $this->config['app']['siteUrl'];
        
//        $this->view->assign('siteUrl', $config['app']['siteUrl']);       
//        $this->view->assign('translator', $this->_translate);   

    }
    
    /**
     * Post-dispatch routines
     * Asignar variables de entorno
     *
     * @return void
     */
    public function postDispatch() {
        parent::postDispatch();
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->yosonVars = $this->_yosonVars;
//        if (APPLICATION_ENV != 'production') {
//            $sep = sprintf('[%s]', strtoupper(substr(APPLICATION_ENV, 0, 3)));
//            $titleStack = $this->view->headTitle()->getContainer()->getValue();
//            if (!is_array($titleStack) || (is_array($titleStack) && isset($titleStack[0]) && $titleStack[0] != $sep) ){
//                $this->view->headTitle()->prepend($sep);
//            }
//        }
        
    }


    /**
     * Retorna la instancia personalizada de FlashMessenger
     * Forma de uso:
     * $this->getMessenger()->info('Mensaje de información');
     * $this->getMessenger()->success('Mensaje de información');
     * $this->getMessenger()->error('Mensaje de información');
     *
     * @return App_Controller_Action_Helper_FlashMessengerCustom
     */
    public function getMessenger() {
        return $this->_flashMessenger;
    }
    /**
     *
     * @see Zend/Controller/Zend_Controller_Action::getRequest()
     * @return Zend_Controller_Request_Http
     */
    public function getRequest() {
        return parent::getRequest();
    }

    /**
     * Retorna un objeto Zend_Config con los parámetros de la aplicación
     *
     * @return Zend_Config
     */
    public function getConfig() {
        return Zend_Registry::get('config');
    }

    /**
     * Retorna el objeto cache de la aplicación
     *
     * @return Zend_Cache_Core
     */
    public function getCache() {
        return Zend_Registry::get('cache');
    }

    /**
     * Retorna el adaptador
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter() {
        return Zend_Registry::get('db');
    }

    /**
     * Retorna el objeto Zend_Log de la aplicación
     *
     * @return Zend_Log
     */
    public function getLog() {
        return Zend_Registry::get('log');
    }

    public function getSession() {
        $session = new Zend_Session_Namespace();
        return $session;
    }
    
    public function addYosonVar($key, $value, $isString = true) {
        $this->_yosonVars[$key] = $isString ? '"'.$value.'"' : $value;
    }
    
    
    public function __call($methodName, $args) {
        $this->redirect('/');
        $this->_forward('error404');
    }
}