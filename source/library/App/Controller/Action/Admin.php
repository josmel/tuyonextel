<?php
class App_Controller_Action_Admin extends App_Controller_Action
{
	public function init()
	{
		parent::init();
		$this->_helper->layout->setLayout('layout-admin');

	}
}