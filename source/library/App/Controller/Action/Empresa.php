<?php
class App_Controller_Action_Empresa extends App_Controller_Action
{
    public function init() 
    {
        parent::init();
        $this->_helper->layout->setLayout('layout-empresa');
    }



}