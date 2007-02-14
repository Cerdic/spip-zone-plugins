<?php

function smslist_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" 
	AND (!isset($GLOBALS['meta']['activer_smslist']) OR $GLOBALS['meta']['activer_smslist']!="non") ) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["smslist_listes_tous"]= new Bouton(
		_DIR_PLUGIN_SMSLIST."img_pack/smslist_liste-24.png",  // icone
		_T("smslist:listes_abonnes") //titre
		);
	}
	return $boutons_admin;
}
?>