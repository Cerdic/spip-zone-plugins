<?php
function horlogeflash($timezone='Europe/Paris', $period=false) {
	$curenttimezone = date_default_timezone_get();
	date_default_timezone_set($timezone);

	$heure   = strftime('%H');
	$minute  = strftime('%M');
	$seconde = strftime('%S');
	$ampm	 = strftime('%p');
	
	date_default_timezone_set($curenttimezone);
	if ($period) {	
		return "$ampm";
	} else {
		return "heure=$heure&amp;minute=$minute&amp;seconde=$seconde";
	}
}

?>