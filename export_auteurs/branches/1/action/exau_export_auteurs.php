<?php

// action/exau_export_auteurs.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/exau_api');

/**
 * renvoyer le contenu export des auteurs
 * Est appele' en ajax
 */
function action_exau_export_auteurs () {

	global $connect_id_auteur, $connect_statut;

	if (!$connect_statut) {
		$auth = charger_fonction('auth', 'inc');
		$auth = $auth();
	}

	if(autoriser('voir', 'auteur', $connect_id_auteur)) {
	
		$securiser_action = charger_fonction('securiser_action', 'inc');
		
		// l'argument récupéré est le statut demandé
		$statut = $securiser_action();

		if($statut = exau_statut_correct ($statut)) {

			include_spip('inc/exau_api');
		
			exau_exporter($statut);
		}
		
	}

	exit;
}

