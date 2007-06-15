<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;

function Inscription2_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration') {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg_inscription2'] =
			new Bouton(
			"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png",  
			_T('inscription2:icone_menu_config'),	
			generer_url_ecrire('cfg', 'cfg=inscription2'),
			NULL,
			'cfg_inscription2'
			);
	}
	return $flux;
}

function Inscription2_ajouter_boutons($boutons_admin){

	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		
		$boutons_admin['auteurs']->sousmenu['inscription2_adherents']= new Bouton(
		"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png", // icone
		_T("inscription2:adherents") //titre
		);
	}
	return $boutons_admin;
}

?>