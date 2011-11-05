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
		// Pré-remplissage des données du profil Facebook
		$flux['data']['nom_inscription'] = _request('nom_inscription');
		$flux['data']['mail_inscription'] = _request('mail_inscription');
		if ($erreur = _request('var_erreur')){
			$flux['data']['message_erreur'] = $erreur;
		}
		else{
			// Un petit message si pas d'erreur
			$flux['data']['message_ok'] = _T('fblogin:form_inscription_preremplir');
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

/*
 * Ajotuer l'UID Facebook à l'auteur nouvellement créé lors de l'inscription
 */
function fblogin_formulaire_traiter($flux){
	if ($flux['args']['form']=='inscription'){
		$nom = _request('nom_inscription');
		$mail = _request('mail_inscription');
		if (function_exists('test_inscription')){
			$f = 'test_inscription';
		}
		else{
			$f = 'test_inscription_dist';
		}
		$desc = $f($mode, $mail, $flux['args']['args'][0], $flux['args']['args'][2]);
		
		// On vérifie que le mail donné dans le formulaire est le même que celui de l'auteur qui vient d'être créé
		// et aussi que le mail d'une connexion Facebook existante.
		// Dans ce cas c'est que le nouvel auteur est bien celui qui est connecté à FB et on lui ajoute son UID
		if (is_array($desc)
			and $mail = $desc['email']
			and include_spip('inc/fblogin')
			and $infos = fblogin_terminer_authentification()
			and is_array($infos)
			and $infos['email'] == $mail
		){
			include_spip('base/abstract_sql');
			sql_updateq(
				'spip_auteurs', 
				array('fb_uid' => $infos['fb_uid']),
				'statut="nouveau" AND email=' . sql_quote($mail)
			);
			$flux['data']['message_ok'] .= '<br/><br/>'._T('fblogin:form_inscription_ok');
		}
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
