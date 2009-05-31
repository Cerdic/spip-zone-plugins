<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: InstallGeneralSetup.class.php,v 1.4 2005/10/27 00:28:45 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/InstallModule.class.php";
require_once INCLUDE_PATH."/core/forms/FormGeneralConfig.class.php";

class InstallGeneralSetup extends InstallModule
{
    var $viewTemplate = "install/general_setup.tpl";
	
	var $stepNumber = 5;
    
	function InstallGeneralSetup()
	{
		
		parent::InstallModule();
		
	}

	function process()
	{
		
		$next_step = false;
		
		$form = new FormGeneralConfig( $this->tpl );

		$done = $form->process();
				
		if($done)
		{
			$next_step = true;
			$this->stepNumber = 6;
		}
		
		$this->tpl->assign("show_next_step", $next_step);
	}
}
?>