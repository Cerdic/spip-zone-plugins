<?php
function horlogeflash($timezone='Europe/Paris', $period=false, $offset=false) {
	$curenttimezone = date_default_timezone_get();
	date_default_timezone_set($timezone);

	$heure   = strftime('%H');
	$minute  = strftime('%M');
	$seconde = strftime('%S');
	$ampm	 = strftime('%p');
	$theoffset = date_offset_get(new DateTime) / 3600;
	
	date_default_timezone_set($curenttimezone);
	if ($period) {	
		return "$ampm";
	} elseif ($offset) {	
		return "$theoffset";
	} else {
		return "heure=$heure&amp;minute=$minute&amp;seconde=$seconde";
	}
}