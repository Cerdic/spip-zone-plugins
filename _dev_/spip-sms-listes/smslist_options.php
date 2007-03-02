<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */
//define('_DEBUG_AUTORISER',true);

function autoriser_smslist_administrer($faire, $type, $id, $qui, $opt) {
	return
		( 
		($qui['statut'] == '0minirezo')
		AND (!$qui['restreint'])
		AND $GLOBALS["options"]=="avancees" 
		AND (!isset($GLOBALS['meta']['activer_smslist']) OR $GLOBALS['meta']['activer_smslist']!="non")
		);
}
function autoriser_smslist_message_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR in_array($opt['nouveau_statut'],array('prop','publie','refuse')) 
	OR !in_array($opt['statut'],array('prepa'))) return false;
	return true;
}
function autoriser_smslist_liste_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR in_array($opt['nouveau_statut'],array('prepa','refuse')) ) return false;
	return true;
}
function autoriser_smslist_abonne_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR in_array($opt['nouveau_statut'],array('prepa')) ) return false;
	return true;
}
function autoriser_smslist_boiteenvoi_donnee_instituer($faire,$type,$id_donnee,$qui,$opt) {
	if (($qui['statut'] != '0minirezo')
	OR !isset($opt['nouveau_statut'])
	OR !in_array($opt['nouveau_statut'],array('prepa','poubelle')) ) return false;
	return true;
}


?>