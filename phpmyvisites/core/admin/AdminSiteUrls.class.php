<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: AdminSiteUrls.class.php,v 1.3 2005/10/27 00:28:43 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/AdminModule.class.php";
require_once INCLUDE_PATH."/core/forms/FormSiteUrls.class.php";

class AdminSiteUrls extends AdminModule
{
    var $viewTemplate = "admin/form.tpl";
    
	function AdminSiteUrls()
	{
		parent::AdminModule();
		
	}

	function process()
	{
		$siteAdmin = $this->needASiteAdminSelected();
		
		if($siteAdmin)
		{
			$form = new FormSiteUrls( $this->tpl, $siteAdmin );
	
			$done = $form->process();
						
			if($done)
			{
				$this->setMessage( );
			}
		}
		$this->site->generateFiles();
	}
}
?>