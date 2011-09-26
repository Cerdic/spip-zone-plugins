<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Afficher le choix Facebook dans le formulaire d'inscription
 *
 * @param <type> $flux
 * @return <type>
 */
function fblogin_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'formulaires/inscription'){
		$fblogin = recuperer_fond('formulaires/inc-inscription-fblogin',$flux['data']['contexte']);
		// Le premier <ul trouvé
		$flux['data']['texte'] = preg_replace('/<ul/i', $fblogin.'<ul', $flux['data']['texte'], 1);
	}
	return $flux;
}

/*
 * Précharge des valeurs si on est dans le cas Facebook
 */
function fblogin_formulaire_charger($flux){
	if ($flux['args']['form'] == 'inscription' and $fb_uid = _request('fb_uid')){
		$flux['data']['fb_uid'] = $fb_uid;
		$flux['data']['nom_inscription'] = _request('nom_inscription');
		$flux['data']['mail_inscription'] = _request('mail_inscription');
		if ($erreur = _request('var_erreur')){
			$flux['data']['message_erreur'] = _request('var_erreur');
		}
	}
	
	return $flux;
}

/*
 * Vérifie si on veut s'inscrire avec Facebook et demande alors l'authentification là-bas
 */
function fblogin_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'inscription' and _request('fblogin')){
		include_spip('inc/fblogin');
		// On ajoute une erreur à tout hasard, pour pas que ça parte dans le traitement
		$flux['data']['fblogin'] = true;
		// On redirige vers l'authentification Facebook
		// et on récupère des informations au retour
		$retour = fblogin_url_retour_inscription(self());
		// Lancer l'identification chez Facebook
		$erreur = fblogin_demander_authentification($retour);
		// Si on arrive ici c'est une ERREUR
		$flux['data']['message_erreur'] = $erreur;
	}
	
	return $flux;
}

/**
 * ajouter l'uid soumis lors de la soumission du formulaire CVT editer_auteur
 * et lors de l'update d'un auteur a l'inscription en 2.1
 * 
 * @param array $flux
 * @return array
 */
function fblogin_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if (!is_null($fb_uid = _request('fb_uid'))) {
			include_spip('inc/fblogin');
			$flux['data']['fb_uid'] = $fb_uid;
		}
	}
	return $flux;
}



?>
