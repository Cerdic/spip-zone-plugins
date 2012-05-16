<?php
	/**
	 *  PHP class SEOstats
	 *
	 *  @package	class.seostats
	 *  @updated	2011/04/29
	 *  @author		Stephan Schmitz <eyecatchup@gmail.com>
	 *  @copyright	2010-present, Stephan Schmitz
	 *  @license	GNU General Public License (GPL)
	 *
	 *  GLOBALS
	 */
 	ini_set('max_execution_time', 180); // on augmente (ou pas!) la durée d'éxecution
 
	define('GOOGLE_TLD', 'com'); // changer en .fr ou autre si besoin
	define('USE_PAGERANK_CHECKSUM_API',false); // false conseillé

	/**
	 *  Options à modifier si utilisation.
	 */
	define('YAHOO_APP_ID',	'XXXXXXXXXX');
	
	define('SEOMOZ_ACCESS_ID','XXXXXXXXXX');
	define('SEOMOZ_SECRET_KEY','XXXXXXXXXX');
?>