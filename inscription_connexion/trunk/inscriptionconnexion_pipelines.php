<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Connecte le visiteur une fois que l'inscription est terminée correctement
 *
 * @pipeline formulaire_traiter
 * @param array $flux
 * @return array
 */
function inscriptionconnexion_formulaire_traiter($flux){
	if (
		$flux['args']['form'] == 'inscription'
		and $id_auteur = intval($flux['data']['id_auteur'])
		and $auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur = '.$id_auteur)
	){
		include_spip('inc/auth');
		auth_loger($auteur);
	}
	
	return $flux;
}
