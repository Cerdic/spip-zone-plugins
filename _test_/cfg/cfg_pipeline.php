<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajoute le bouton d'amin aux webmestres
function cfg_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg'] =
			new Bouton(
			_DIR_PLUGIN_CFG."cfg-22.png",  // icone
			_L('CFG'),	// titre
			generer_url_ecrire('cfg'),
			NULL,
			'cfg'
			);
	}
	return $flux;
}


?>
