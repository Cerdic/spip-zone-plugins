<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inserer le script js dans l'espace prive
 * @param $flux
 * @return string
 */
function pb_couleur_rubrique_header_prive($flux){
	$flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_PB_COULEUR_RUBRIQUE . 'javascript/pb_couleur_rubrique.js"></script>';
	return $flux;
}


function pb_couleur_rubrique_affiche_droite($flux){

	$exec = $flux["args"]["exec"];
	if ($exec=="rubrique"){
		$id_rubrique = $flux["args"]["id_rubrique"];
		// si la config est sur "oui, que les secteurs"
		if (lire_config('pb_couleur_rubrique/secteurs')=='oui'){
			// calcul du secteur
			$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique));
			// on affiche que dans le secteur
			if ($id_secteur==$id_rubrique){
				$contexte = array('id_rubrique' => $id_rubrique);
				$flux["data"] .= recuperer_fond("inclure/couleur_rubrique", $contexte);
			}
		} else {
			$contexte = array('id_rubrique' => $id_rubrique);
			$flux["data"] .= recuperer_fond("inclure/couleur_rubrique", $contexte);
		}
	}
	// quoi qu'il en soit, la couleur du site sera toujours
	if ($exec=="rubriques"){
		$contexte = array('id_rubrique' => '0');
		$flux["data"] .= recuperer_fond("inclure/couleur_rubrique", $contexte);
	}
	return $flux;
}