<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// À chaque hit en partie publique, on va chercher le panier du visiteur actuel, et sinon le créer
if (_DIR_RESTREINT) {
	include_spip('inc/paniers');
	include_spip('inc/session');
	// Si on a bien un panier
	if (_request('action') != 'cron' and $id_panier = paniers_id_panier_encours()){
		// On met le panier dans la session
		session_set('id_panier', $id_panier);
	}
	else{
		session_set('id_panier');
	}
}

?>
