<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: AdminGroup.class.php,v 1.2 2005/10/29 19:35:54 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/AdminModule.class.php";
require_once INCLUDE_PATH."/core/forms/FormGroup.class.php";
require_once INCLUDE_PATH . "/core/include/UserConfigDb.class.php";	

class AdminGroup extends AdminModule
{
    var $viewTemplate = "admin/group.tpl";
    
	function AdminGroup()
	{
		parent::AdminModule();
	}

	function process()
	{				
		$siteAdmin = $this->needASiteAdminSelected();
		
		if($siteAdmin)
		{
			$form = new FormGroup( $this->tpl, $siteAdmin );
	
			$done = $form->process();
				
			if($done)
			{
				$this->setMessage( );
			}
		}
	}
}
?>