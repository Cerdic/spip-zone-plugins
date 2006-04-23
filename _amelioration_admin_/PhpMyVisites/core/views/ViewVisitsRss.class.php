<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ViewVisitsRss.class.php,v 1.9 2005/11/13 23:13:24 matthieu_ Exp $


require_once INCLUDE_PATH."/core/include/ViewModule.class.php";

class ViewVisitsRss extends ViewModule
{
	function ViewVisitsRss($o_site = null)
	{
		parent::ViewModule( "visits" );
		$this->site = $o_site;
		$this->disableCache = true;
	}
	
	function showAll( $return = false, $mail = false )
	{
	    $this->viewTemplate = ($mail) 
						? "common/structure_mail.tpl" 
						: "common/structure_rss.tpl";

		$date = $this->data->archive->date->get();
		$this->tpl->assign( "url_phpmv",
				generer_url_ecrire('phpmv',
				"site=".$this->site->getId(). 
				"&mod=view_visits&date=" . $date,true)
								/*Request::getCurrentCompletePath() 
									. "/index.php?site=".$this->site->getId()
									. "&mod=view_visits&date="
									. $date*/);
		$methods = array(
			"statistics" => array(),
//			"periodsummaries" => array(8)
		);
		$this->getDataMethod( $methods );
		
		if($return)
		{
			return $this->fetch($this->viewTemplate);
		}
		else
		{
			$this->display();
			return true;
		}
	}
}
?>
