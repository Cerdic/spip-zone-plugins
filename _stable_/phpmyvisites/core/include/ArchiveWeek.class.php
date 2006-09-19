<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ArchiveWeek.class.php,v 1.1 2005/10/08 02:57:52 matthieu_ Exp $


/**
* Class that is used for the archiving of week periods
* It uses ArchivePeriod which makes all computes based on day archives 
*/
class ArchiveWeek extends ArchivePeriod
{
	function ArchiveWeek($site, $s_date)
	{		
		parent::Archive($site);
		
		$this->setPeriodType(DB_ARCHIVES_PERIOD_WEEK);
		
		$this->getPeriodDatesLimit($s_date);
	}
}
?>