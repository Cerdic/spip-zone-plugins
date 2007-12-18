<?php
/*
	Mini Calendrier pour Alternatives
 	Patrice VANNEUFVILLE - patrice.vanneufville(!arob!)laposte.net
	(c) 2007 - Distribue sous licence GPL
	Plugin pour spip 1.9
	Licence GNU/GPL
*/

function url_evenements($D, $M, $Y) {
 $Time = mktime(0, 0, 0, $M, $D, $Y);
 return parametre_url(generer_url_public('evenements'),VAR_DATE_CAL,
 	sprintf("%04d-%02d-%02d",intval(date('Y', $Time)),intval(date('m', $Time)),intval(date('d', $Time)))
 );
}
		  
// variable utilisee dans par le calendrier dans les urls
define('VAR_DATE_CAL', 'archives');
?>