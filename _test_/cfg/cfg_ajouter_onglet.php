<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg'] =
			new Bouton(
			"plugin-24.gif",  // icone
			_L('CFG'),	// titre
			generer_url_ecrire('cfg'),
			NULL,
			'cfg'
			);
	}
	return $flux;
}

?>