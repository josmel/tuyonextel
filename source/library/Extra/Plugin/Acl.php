<?php

class Extra_Plugin_Acl
        extends Zend_Controller_Plugin_Abstract
{

    private $_noauth = array('module' => 'auth',
        'controller' => 'index',
        'action' => 'login');
    
    private $_exception = array(
        'mvc:auth/index/sinacceso',      
        'mvc:auth/index/logout',
        'mvc:auth/index/login',
        );
    
    private $_noacl = array('module' => 'admin',
        'controller' => 'index',
        'action' => 'index');
    protected $_acl;
    
    protected $_role;
    
    private $_module;
    
    private $_controller;
    
    private $_action;

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {        
        
        $this->_module =  $request->getModuleName();
        $this->_controller =  $request->getControllerName();
        $this->_action =  $request->getActionName();
        
        //$this->setAcl(Zend_Registry::get('Acl'));	
        $auth = Zend_Auth::getInstance();        
        
        if ($auth->hasIdentity()) {    
        	    	
            $user = $auth->getStorage()->read();            
                        
            $tipoUsuario = $user->tipousuario;
            $module = $this->_module;
            
            if ($tipoUsuario == 1) {
                if ($module == "admin") {
                    $request->setModuleName("admin");
                    $request->setControllerName($this->_controller);
                    $request->setActionName($this->_action);
                    return;
                }
                
            }
            if ($tipoUsuario == 2) {
                if ($module == "empresa") {
                    $request->setModuleName("empresa");
                    $request->setControllerName($this->_controller);
                    $request->setActionName($this->_action);
                    return;
                } 
                if ($module == "admin") {
                    $request->setModuleName("admin");
                    $request->setControllerName("auth");
                    $request->setActionName($this->_action);
                    return;                
                }
            }
            if ($tipoUsuario == 3) {
                if ($module == "operador") {
                    $request->setModuleName($module);
                    $request->setControllerName($this->_controller);
                    $request->setActionName($this->_action);
                    return;
                } 
                if ($module == "admin") {
                    $request->setModuleName($module);
                    $request->setControllerName("auth");
                    $request->setActionName($this->_action);
                    return;                
                }
            }
            if ($tipoUsuario == 4) {
            	if ($module == "supervisor") {
            		$request->setModuleName($module);
            		$request->setControllerName($this->_controller);
            		$request->setActionName($this->_action);
            		return;
            	}
            	if ($module == "admin") {
            		$request->setModuleName($module);
            		$request->setControllerName("auth");
            		$request->setActionName($this->_action);
            		return;
            	}
            }
            
            
        } else {            
            
            if ($this->_module == 'admin') {                
                $request->setModuleName('admin');
                $request->setControllerName('auth');
                $request->setActionName('index');
                return;
            } else {                
                $request->setModuleName('default');
                $request->setControllerName($this->_controller);
                $request->setActionName($this->_action);
                return;
            }
            
        }
        
    }

    function getAcl()
    {
        return $this->_acl;
    }

    function getRole()
    {
        return $this->_role;
    }

    function setRole($role)
    {
        $this->_role = $role;
    }

    function setAcl($acl)
    {
        $this->_acl = $acl;
    }

}
