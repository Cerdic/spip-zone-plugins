<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Impossible de s'inscrire avec un email déjà existant avec ce plugin
 *
 * @pipeline formulaire_traiter
 * @param array $flux
 * @return array
 */
function inscriptionconnexion_formulaire_verifier($flux){
	if (
		$flux['args']['form'] == 'inscription'
		and $email = _request('mail_inscription')
		and $auteur = sql_fetsel('*', 'spip_auteurs', 'email = '.sql_quote($email))
	){
		$flux['message_erreur'] = _T('inscriptionconnexion:erreur_compte_existant');
	}
	
	return $flux;
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
		// Quand on décide d'utiliser ce plugin, c'est qu'on veut outrepasser la confirmation par email
		// donc on le fait ici, il faut confirmer son statut avant de connecter
		include_spip('action/inscrire_auteur');
		$auteur = confirmer_statut_inscription($auteur);
		
		// Puis on connecte
		include_spip('inc/auth');
		auth_loger($auteur);
	}
	
	return $flux;
}
