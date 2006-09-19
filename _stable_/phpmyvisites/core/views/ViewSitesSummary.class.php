<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ViewSitesSummary.class.php,v 1.4 2005/10/08 02:57:55 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/ViewModule.class.php";

class ViewSitesSummary extends ViewModule
{
    var $viewTemplate = "common/viewsitessummary_all.tpl";
    
	function ViewSitesSummary()
	{
		parent::ViewModule( "sitessummary" );
	}
	
	function process()
	{
		$methods = array(
			"statistics" => array(),
		);
		$this->getDataMethod( $methods );
	}	
}
?>