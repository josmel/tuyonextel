<?php

class Core_Controller_ActionAdmin extends Core_Controller_Action {
    
    protected $_identity;
    
    public function init() { 
        parent::init();
        $this->_helper->layout->setLayout('layout-admin');
    }
    
    public function preDispatch() {     
        parent::preDispatch();
        
        $this->permisos();
        $this->_identity = Zend_Auth::getInstance()->getIdentity();                        
        $this->view->menu=$this->getMenu();        
        $this->view->identity=$this->_identity;
    }
    
    function permisos() {
        $auth = Zend_Auth::getInstance();
        $controller=$this->_request->getControllerName();
        if ($auth->hasIdentity()) {                    
        }else{
            if ($controller!='index') {
                $this->_redirect('/');
            }
        }
    }
    
    function getMenu() {
        $menu = array(
            'dashboard'=>
            array('class'=>'icad-dashb','url'=>'/dashboard','title'=>'Dashboard'),
            'cp'=>
            array('class'=>'icad-prom','url'=>'/cp','title'=>'(CP) Consulta previa '),
            'sp'=>
            array('class'=>'icad-prom','url'=>'/sp','title'=>'(SP) Solicitud de Portabilidad'),
            'ecpc'=>
            array('class'=>'icad-prom','url'=>'/ecpc','title'=>'(ECPC) Solicitud de Consulta previa'),
            'esc'=>
            array('class'=>'icad-prom','url'=>'/esc','title'=>'(ESC) Solicitud al Cedente'),
             'customer'=>
            array('class'=>'icad-prom','url'=>'/customer','title'=>'Clientes')
        
        
        );
        return $menu;
    }
     
    public function auth($usuario,$password,$url=null) {              
        $dbAdapter = Zend_Registry::get('multidb');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter
            ->setTableName('tusers')
            ->setIdentityColumn('login')
            ->setCredentialColumn('password')
            ->setIdentity($usuario)
            ->setCredential($password);
        try {
            $select = $authAdapter->getDbSelect();
            $select->where('state = 1');
            //echo $select->assemble(); //exit;
            //var_dump($authAdapter); exit;
            $result = Zend_Auth::getInstance()->authenticate($authAdapter);
            //var_dump($result); exit;
            if ($result->isValid()){
                $storage = Zend_Auth::getInstance()->getStorage();
                $bddResultRow = $authAdapter->getResultRowObject();
                $storage->write($bddResultRow);
                $msj = 'Bienvenido Usuario '.$result->getIdentity();
                //$this->_flashMessenger->success($msj);
                $this->_identity = Zend_Auth::getInstance()->getIdentity(); 
                if(!empty($url)){
                    $this->_redirect($url);
                }
                $return = true;
            } else { 
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $msj = 'El usuario no existe';
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $msj = 'Password incorrecto';
                        break;
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $msj = 'dsdsdsd';
                        break;
                    default:
                        $msj='Datos incorrectos';
                        break;
                }
                $this->_flashMessenger->warning($msj);
                $return = false;
            }
        } catch(Exception $e) {
            echo $e->getMessage();exit;
        }
        
        return $return;
    }
}
