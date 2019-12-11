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
		// D'après mes sources sur place, le décalage en hiver n'est que de 5h avec Baïkonour
		if (($timezone === 'Asia/Almaty')
			and
			(!date('I', time()))
		) {
			$theoffset = 5;
		}
		return "$theoffset";
	} else {
		return "heure=$heure&amp;minute=$minute&amp;seconde=$seconde";
	}
}