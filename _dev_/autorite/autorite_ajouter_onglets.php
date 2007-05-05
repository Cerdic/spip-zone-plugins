<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;

function Autorite_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('webmestre')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg_autorite'] =
			new Bouton(
			"../"._DIR_PLUGIN_AUTORITE."illuminati-24.gif",  // icone
			_T('autorite:icone_menu_config'),	// titre
			generer_url_ecrire('cfg', 'cfg=autorite'),
			NULL,
			'cfg_autorite'
			);
	}
	return $flux;
}

?>