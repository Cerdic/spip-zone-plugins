<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ViewPagesDetails.class.php,v 1.4 2005/12/21 00:54:08 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/ViewModule.class.php";

class ViewPagesDetails extends ViewModule
{
    var $viewTemplate = "common/viewpages_details.tpl";
    
	function ViewPagesDetails()
	{
		parent::ViewModule( "pagesdetails");
	}
	
	function process()
	{
		$categoryZoomAsked = html_entity_decode($this->request->getCategoryAsked());
		$idToUse = $this->request->getIdToUse();
		
		$this->tpl->assign('level', 1 + substr_count($categoryZoomAsked, '>'));
		$this->tpl->assign('idtouse', $idToUse);
		
		$methods = array("zoomsorted" => array($categoryZoomAsked, $idToUse));
		$this->getDataMethod( $methods );
	}
	
	function init($request)
	{
	    parent::init($request);
		$this->tpl->caching = 0;
	}
}
?>