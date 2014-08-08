<?php

class Core_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    
    /**
     *
     * @var Zend_Acl
     */
    protected $_acl;
    
    protected $_role;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        
        $roles = array(Core_Acl::GUEST);
        //var_dump($auth->getIdentity()); exit;
        if ($auth->hasIdentity()){
            if(Zend_Registry::get('domain') == 'admin') {
                $modelRol = new Application_Model_Role();
                $roles = $modelRol->getRolesByUser($auth->getIdentity()->iduser);
                if(count($roles) == 0)
                    $roles = array(Core_Acl::GUEST);
            }
        }
        $this->setAcl(Zend_Registry::get('Zend_Acl'));
        
        $request=$this->getRequest();
         
        //Check if the request is valid and controller an action exists. If not redirects to an error page.
        if (!$this->isValidResource($request)){
            
            if (!$auth->hasIdentity()){
                $this->getResponse()->setRedirect('/');
                return;
            }
            $request->setControllerName('error');
            $request->setActionName('error');
            
            throw new Exception("La ruta solicitada no existe.");
            return;
        }
    
        $continue = false;
        foreach ($roles as $rol) {
            $this->setRole($rol);
            //Check if user is allowed to acces the url and redirect if needed
            if ($this->hasAccessUrl($request)){
               $continue = true; 
            }
        
        }
        
        if (!$continue) {
            if (!$auth->hasIdentity()){
                $this->getResponse()->setRedirect('/');
                return;
            }
            $request->setControllerName('error');
            $request->setActionName('error');
            throw new Exception("Acceso denegado.");
            //$request->setActionName('access-denied');
            return;  
        }
    }
    
    function isValidResource(Zend_Controller_Request_Abstract $request) {        
        $acl = $this->getAcl();
        $url1 = $request->getModuleName() . '::*';
        $url2 = $request->getModuleName() . '::' . $request->getControllerName() . '::*';
        $url3 = $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName();

        return $acl->has($url1) || $acl->has($url2) || $acl->has($url3);
    }
    
    function hasAccessUrl(Zend_Controller_Request_Abstract $request) {
        $acl = $this->getAcl();
        $url1 = $request->getModuleName() . '::*';
        $url2 = $request->getModuleName() . '::' . $request->getControllerName() . '::*';
        $url3 = $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName();

        return ($acl->has($url1) && $acl->isAllowed($this->getRole(), $url1))
                || ($acl->has($url2) && $acl->isAllowed($this->getRole(), $url2))
                || ($acl->has($url3) && $acl->isAllowed($this->getRole(), $url3));
    }
    
    function getAcl() {
        return $this->_acl;
    }

    function getRole() {
        return $this->_role;
    }

    function setRole($role) {
        $this->_role = $role;
    }

    function setAcl($acl) {
        $this->_acl = $acl;
    }
}
