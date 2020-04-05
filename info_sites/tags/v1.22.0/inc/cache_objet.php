<?php

/**
 * On invalide le cache d'un objet sur les formulaires.
 * Cela permet entre que les nouveaux objets associés soient affichés sur la page dudit objet.
 * 
 * Soit on a un paramètre associer_objet dans l'environnement, on peut ainsi déterminer l'objet et son id.
 * Soit on a les paramètres objet/id_objet dans l'environnement.
 * 
 * @return void
 */
function inc_cache_objet_dist() {
	include_spip('inc/utils');
	$associer_objet = _request('associer_objet');
	if ($associer_objet and preg_match('/|/', $associer_objet)) {
		$associer_objet = explode('|', $associer_objet);
		list($objet, $id_objet) = $associer_objet;
	} else {
		$objet = _request('objet');
		$id_objet = _request('id_objet');
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");
}
