<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ViewPages.class.php,v 1.4 2005/10/08 02:57:55 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/ViewModule.class.php";

class ViewPages extends ViewModule
{
    var $viewTemplate = "common/viewpages_all.tpl";
        
	function ViewPages( )
	{
		parent::ViewModule( "pages");
	}
	
	function process()
	{
		$this->tpl->assign('level', 0);		
		
		$methods = array(
		  "zoom" => array('')
		);
		
		$this->getDataMethod( $methods );
	}		
}
?>