<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function _initConfig() {
        $config = new Zend_Config($this->getOptions(), true);
        $inifiles = array('app', 'cache', 'private'); //TODO: only load cache.ini for models
        foreach ($inifiles as $file) {
            $inifile = APPLICATION_PATH . "/configs/$file.ini";
            if (is_readable($inifile))
                $config->merge(new Zend_Config_Ini($inifile));
        }
        $config->setReadOnly();
        $this->setOptions($config->toArray());
        Zend_Registry::set('config', $config);
        define('DATE_DB', 'Y-m-d H:i:s');
    }

    public function _initView() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $v = $layout->getView();
        $v->addHelperPath('App/View/Helper', 'App_View_Helper');
        $config = Zend_Registry::get('config');
       $this->getResourceLoader()->addResourceType('entity', 'entitys/', 'Entity');
        
        //Definiendo Constante para Partials
        define('MEDIA_URL', $config->app->mediaUrl);
        define('ELEMENTS_URL', $config->app->elementsUrl);
        define('SITE_URL', $config->app->siteUrl);

        // Config Built-in View Helpers
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype(Zend_View_Helper_Doctype::HTML5);
        $v->headTitle($config->resources->view->title)->setSeparator(' - ');
        $v->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        //$v->headMeta()->appendName("author", "jotiniano");
        $v->headMeta()->appendName("og:image", "http://www.moso.pe/img/moso-logo.png");
        $v->headMeta()->appendName("viewport", "width=device-width, minimum-scale=1.0, maximum-scale=1.0");
        $v->headMeta()->appendName("description", "Portal WAP - Movistar  M&uacute;sica"); //
        $v->headMeta()->setCharset("utf-8");
    }

    public function _initRegistries() {
        $config = Zend_Registry::get('config');
        
        $this->_executeResource('db');
        $adapter = $this->getResource('db');
        Zend_Registry::set('db', $adapter);
        
        $this->_executeResource('cachemanager');
        $cacheManager = $this->getResource('cachemanager');
        Zend_Registry::set('cache', $cacheManager->getCache($config->app->cache));

        $this->_executeResource('log');
        $log = $this->getResource('log');
        Zend_Registry::set('log', $log);
    }

    public function _initActionHelpers() {
        // Adding hook action helpers
        Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Auth());
        Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Security());
    }

}

