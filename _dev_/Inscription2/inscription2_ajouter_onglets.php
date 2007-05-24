<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;

function Inscription2_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration') {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg_inscription2'] =
			new Bouton(
			"../"._DIR_PLUGIN_INSCRIPTION2."inscription2_icone.png",  
			_T('inscription2:icone_menu_config'),	
			generer_url_ecrire('cfg', 'cfg=inscription2'),
			NULL,
			'cfg_inscription2'
			);
	}
	return $flux;
}

?>