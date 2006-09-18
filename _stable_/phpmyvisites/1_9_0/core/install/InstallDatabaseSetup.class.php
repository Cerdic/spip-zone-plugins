<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: InstallDatabaseSetup.class.php,v 1.3 2005/10/27 00:28:45 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/InstallModule.class.php";
require_once INCLUDE_PATH."/core/forms/FormDbConfig.class.php";

class InstallDatabaseSetup extends InstallModule
{
    var $viewTemplate = "install/database_setup.tpl";
	
	var $stepNumber = 3;
    
	function InstallDatabaseSetup()
	{
		parent::InstallModule();
	}

	function process()
	{
		
		$next_step = false;
		
		$form = new FormDbConfig( $this->tpl );

		$done = $form->process();
				
		if($done && empty($GLOBALS['header_error_message_tpl']))
		{
			$next_step = true;
		}
		
		$this->tpl->assign("show_next_step", $next_step);
	}
}
?>