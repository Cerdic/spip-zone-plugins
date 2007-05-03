<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;

function Autorite_ajouterBoutons($boutons_admin) {
	if (autoriser('webmestre')) {
		// on voit le bouton dans la barre "configurer"
		$boutons_admin['configuration']->sousmenu['autorite']= new Bouton(
			"../"._DIR_PLUGIN_AUTORITE."illuminati-24.gif",  // icone
			_T('autorite:icone_menu_config'),	// titre
			'cfg&cfg=autorite' // exec
			);
	}
	return $boutons_admin;
}

?>