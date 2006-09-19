<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Contacts.class.php,v 1.8 2005/11/21 06:07:25 matthieu_ Exp $


class Contacts extends Module
{
	var $viewTemplate = "common/contacts.tpl";
	
	function Contacts()
	{
		parent::Module();
	}
	
	function showAll()
	{
		if (!$this->isCached()){
			$ctrl =& ApplicationController::getInstance();
			$o_lang = $ctrl->getLang();
			
			$this->tpl->assign("link_forums", HREF_FORUMS);
			$this->tpl->assign("translators_array", $o_lang->getArrayTranslators());
		}
		$this->display();
	}
}
?>