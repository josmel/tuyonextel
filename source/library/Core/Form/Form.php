<?php

class Core_Form_Form extends Zend_Form
{
    
    function init() {
        parent::init();                
    }
    
    public function addDecoratorCustom($file) {
        $this->setDecorators(array(array('ViewScript',array('viewScript'=>$file))));
    }            
}


?>
