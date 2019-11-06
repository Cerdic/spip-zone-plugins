<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function mailshot_autoriser(){}


function autoriser_mailshot_iconifier_dist() {return false;}

/*
// TODO ?
function autoriser_mailshot_voir_dist($faire,$quoi,$id,$qui){
	// OK tout le monde ?
	return true;
	// Seulement les administrateurs ?
	return autoriser('configurer',$quoi,$id,$qui);
}
*/

function autoriser_mailshot_archiver_dist($faire,$quoi,$id,$qui){
	if ($qui['statut']=='0minirezo'
	  AND !$qui['restreint']
	  AND $statut = sql_getfetsel('statut','spip_mailshots','id_mailshot='.intval($id))
	  AND in_array($statut,array('end','cancel'))){
		return true;
	}
	return false;
}

