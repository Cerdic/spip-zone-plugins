<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: AdminGeneralConfig.class.php,v 1.6 2005/10/27 00:28:43 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/AdminModule.class.php";
require_once INCLUDE_PATH."/core/forms/FormGeneralConfig.class.php";

class AdminGeneralConfig extends AdminModule
{
    var $viewTemplate = "admin/generalconfig.tpl";
    
	function AdminGeneralConfig()
	{
		parent::AdminModule();
	}

	function process()
	{				
		$form = new FormGeneralConfig( $this->tpl );

		$done = $form->process();
				
		if($done)
		{
			$this->setMessage( );
		}
	}
}
?>