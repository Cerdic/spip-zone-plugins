<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: browserFamilies.php,v 1.4 2006/01/04 22:32:48 matthieu_ Exp $



// source: http://en.wikipedia.org/wiki/List_of_web_browsers

$GLOBALS['browserFamilies'] = array(
	"ie"	=> array("IE"),
	"gecko" => array("NS", "PX", "FF", "FB", "CA", "CH", "GA", "KM", "MO", "SM"),
	"khtml" => array("SF", "KO", "OW"),
	"opera" => array("OP")
);

$GLOBALS['browsers_type_display'] = array(
	'ie' => 'Internet Explorer',
	'gecko' => 'Gecko (Mozilla, Netscape)',
	'khtml' => 'Khtml (Konqueror, Safari)',
	'opera' => 'Opera'
	);

?>