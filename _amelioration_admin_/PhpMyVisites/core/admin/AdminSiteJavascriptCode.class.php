<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: AdminSiteJavascriptCode.class.php,v 1.2 2005/10/07 00:38:20 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/AdminModule.class.php";

class AdminSiteJavascriptCode extends AdminModule
{
    var $viewTemplate = "admin/site_javascript_code.tpl";
    
	function AdminSiteJavascriptCode()
	{
		parent::AdminModule();		
	}

	function process()
	{
		$siteAdmin = $this->needASiteAdminSelected();
		
		if($siteAdmin)
		{			
			$this->tpl->assign('js_code', getJavascriptCode( $siteAdmin ));
		}
	}
}
?>