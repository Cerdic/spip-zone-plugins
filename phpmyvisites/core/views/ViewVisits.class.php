<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ViewVisits.class.php,v 1.5 2005/10/08 02:57:55 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/ViewModule.class.php";

class ViewVisits extends ViewModule
{
    var $viewTemplate = "common/viewvisits_all.tpl";
    
    
	function ViewVisits()
	{
		parent::ViewModule("visits");
	}
	
	function process()
	{
		// TEST GRAPHS	
		/*
		$data = $this->data->getSitesSummaryStatisticsGraph();
		printDebug($data);
		$n = 6;
		$m = 'a';
		$a = _PHPMV_DIR_DATA . "/g".$n.$m.".php";
		saveConfigFile($a, $data , 'data');
		exit;
		*/
				
		$methods = array(
		"statistics" => array(),
		"periodsummaries" => array(8)
		);
		$this->getDataMethod( $methods );
	}
	
}
?>
