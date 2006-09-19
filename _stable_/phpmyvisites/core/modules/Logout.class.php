<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Logout.class.php,v 1.2 2005/12/22 20:43:53 matthieu_ Exp $

class Logout extends Module
{
	var $viewTemplate = '';
	
	function Logout()
	{
		parent::Module();
	}
	
	function showAll()
	{
	}
	
	function init($request)
	{
	    parent::init($request);
		
		$ck = new Cookie( COOKIE_NAME_SESSION );

		$ck->delete();
		
		Request::redirectToModule('index');
	}
}
?>