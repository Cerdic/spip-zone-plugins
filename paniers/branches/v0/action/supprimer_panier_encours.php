<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_supprimer_panier_encours_dist() {
	include_spip('inc/paniers');
	
	// Si on trouve un panier pour le visiteur actuel
	if ($id_panier = paniers_id_panier_encours()){
		// On le supprime
		$action = charger_fonction('supprimer_panier', 'action/');
		$action($id_panier);
	}
}

?>
