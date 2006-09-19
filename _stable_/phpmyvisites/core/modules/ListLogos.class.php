<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ListLogos.class.php,v 1.1 2005/11/21 06:07:25 matthieu_ Exp $


class ListLogos extends Module
{
	var $viewTemplate = "common/list_logos.tpl";
	
	function ListLogos()
	{
		parent::Module();
	}
	
	function showAll()
	{
		$this->tpl->setMainTemplate('common/list_logos.tpl');
		$this->tpl->assign("content", getDisplayLogosListing());
		$this->display();
	}
}
?>