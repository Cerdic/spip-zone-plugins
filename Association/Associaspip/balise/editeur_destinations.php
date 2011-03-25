<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/association_comptabilite');

function balise_EDITEUR_DESTINATIONS_dist ($p) {
	/* on recupere dans l'environement id_dest,montant_dest, unique_dest et defaut_dest qui doivent donc etre assignees par la fonction charger du formulaire contenant la balise */
      return calculer_balise_dynamique($p, 'EDITEUR_DESTINATIONS', array('id_dest', 'montant_dest', 'unique_dest', 'defaut_dest'));
}

function balise_EDITEUR_DESTINATIONS_dyn($id_dest, $montant_dest, $unique_dest, $defaut_dest) {
	if (($id_dest) && ($montant_dest)) {
		$destinations_id_montant = array();
		foreach ($id_dest as $k => $v) {
			$destinations_id_montant[$v] = $montant_dest[$k];
		}
	} else {
		$destinations_id_montant = '';
	}
	
	return association_editeur_destinations($destinations_id_montant, $unique_dest, $defaut_dest);
}
?>
