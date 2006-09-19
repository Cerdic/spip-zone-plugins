<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: InstallFinish.class.php,v 1.3 2005/10/07 00:38:21 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/InstallModule.class.php";

class InstallFinish extends InstallModule
{
    var $viewTemplate = "install/finish.tpl";
    
	var $stepNumber = 9;
	
	function InstallFinish()
	{
		parent::InstallModule();
	}

	function process()
	{
		$conf =& PmvConfig::getInstance();
		
		$conf->update( array( 'install_ok' => true ) );
		$conf->write();
	}
}
?>