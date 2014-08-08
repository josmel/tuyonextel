<?php

class Default_GoController extends App_Controller_Action_Portal
{

    public function init()
    {    	
        parent::init();     
    }
    
    public function indexAction()
    {
    	
    	$enlace = $this->getParam('enlace');
    	$perfil = $this->getParam('perfil');

    	if ($this->validaPerfil($perfil) and $enlace) {    		
    		$model = new App_Model_ConfigPerfil();
    		$data['perfil'] = $perfil;
    		$result = $model->registerCdr($_SERVER['HTTP_USER_AGENT'], $data, $enlace);
    		
    		if ($result)
    		    $this->_redirect($enlace);
    		else 
    		    $this->_redirect('/index');	
    		
    	} else {
    		$this->_redirect('/index');
    	}
    	
    }
    
    function validaPerfil($perfil){
    	
    	$perfil = (int) $perfil;
    	if ($perfil < 1 or $perfil > 4)
    	     $perfil = 0;
    	     
    	     return $perfil;
    }
    

}

