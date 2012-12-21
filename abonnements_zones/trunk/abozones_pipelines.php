<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Afficher un formulaire de liaison de zones sur les offres d'abonnement
 */
function abozones_afficher_complement_objet($flux){
	// Si on est en train de visualiser une offre d'abonnement
	if ($flux['args']['type'] == 'abonnements_offre'){
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/abonnements_offre-zones',
			array('id_abonnements_offre' => $flux['args']['id'])
		);
	}
	
	return $flux;
}

?>
