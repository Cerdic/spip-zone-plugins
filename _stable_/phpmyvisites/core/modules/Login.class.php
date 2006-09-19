<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Login.class.php,v 1.5 2005/10/08 02:57:53 matthieu_ Exp $


require_once INCLUDE_PATH."/core/forms/FormLogin.class.php";

class Login extends Module
{
	var $viewTemplate = '';
	
	function Login()
	{
		parent::Module();
	}
	
	function showAll()
	{
		$form = new FormLogin( $this->tpl );
		$done = $form->process();
		
		$this->tpl->assign("error_login", $this->request->getErrorLogin());
		$this->display();
	}
	
	
	function init($request)
	{
	    parent::init($request);
		$this->tpl->caching = 0;
		$this->tpl->setMainTemplate( "common/login.tpl");
	}
}
?>