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

function Inscription2_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'auteurs_edit':
			case 'auteur_infos':
				include_spip('inc/inscription2_fiche_adherent');
				$id_auteur = $flux['args']['id_auteur'];
				$flux['data'] .= inscription2_fiche_adherent($id_auteur);
				break;
			default:
				break;
		}

		return $flux;
	}

?>