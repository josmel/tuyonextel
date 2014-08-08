<?php
class App_Controller_Action_Supervisor extends App_Controller_Action
{
    public function init() 
    {
        parent::init();
        $this->_helper->layout->setLayout('layout-supervisor');
    }
}