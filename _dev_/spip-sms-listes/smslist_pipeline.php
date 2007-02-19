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

function smslist_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" 
	AND (!isset($GLOBALS['meta']['activer_smslist']) OR $GLOBALS['meta']['activer_smslist']!="non") ) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["smslist_listes_tous"]= new Bouton(
		_DIR_PLUGIN_SMSLIST."img_pack/spip-sms-listes-24.gif",  // icone
		_T("smslistliste:listes_abonnes") //titre
		);
	}
	return $boutons_admin;
}


?>