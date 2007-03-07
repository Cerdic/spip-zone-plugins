<?php
/*
 * Spip-Outliner
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function outliner_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" 
	) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["spip_outliner"]= new Bouton(
		_DIR_PLUGIN_SMSLIST."img_pack/spip-outliner-24.gif",  // icone
		_T("outline:spip_outliner") //titre
		);
	}
	return $boutons_admin;
}


?>