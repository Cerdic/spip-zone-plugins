<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ArchiveMonth.class.php,v 1.2 2005/11/13 23:13:19 matthieu_ Exp $


/**
 * Class that is used for the archiving of month periods
 * It uses ArchivePeriod which makes all computes based on day archives 
 * and It is used for ArchiveYear
 */
class ArchiveMonth extends ArchivePeriod
{
	/**
	 * Constructor
	 * 
	 * @param object $site
	 * @param string $s_date
	 */
	function ArchiveMonth($site, $s_date='')
	{
		//print("site ".$site->getName()." date $s_date <br>");
		
		parent::Archive($site);
		
		$this->setPeriodType(DB_ARCHIVES_PERIOD_MONTH);
		
		if(!empty($s_date))
		{
			$this->setDate($s_date);
		}
		
	}
}
?>