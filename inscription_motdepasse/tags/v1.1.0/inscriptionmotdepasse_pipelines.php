<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Charge les deux champs de mot de passe au démarrage du formulaire d'inscription
 *
 * @param array $flux
 * @return array
 */
function inscriptionmotdepasse_formulaire_charger($flux){
	// Seulement si le formulaire d'origine n'a pas renvoyé un vrai "false"
	if ($flux['args']['form'] == 'inscription' and $flux['data'] !== false) {
		$flux['data']['password'] = '';
		$flux['data']['password_confirmation'] = '';
	}
	
	// gestion du cas : oubli du mot de passe avant confirmation du compte
	if ($flux['args']['form'] == 'mot_de_passe'){
		$row = retrouve_auteur(null,  _request('p'));
		if ($row && ($id_auteur = $row['id_auteur']) && $row['prefs'] == '6forum'){
			confirmer_statut_inscription($row);
		}
	}	
	
	return $flux;
}

/**
 * Ajoute le HTML des deux champs de mot de passe durant l'inscription
 *
 * @param array $flux
 * @return array
 */
function inscriptionmotdepasse_formulaire_fond($flux){
	if ($flux['args']['form'] == 'inscription'){
		$champs_password = recuperer_fond('formulaires/inc-inscriptionmotdepasse', $flux['args']['contexte']);
		
		// S'il y a le plugin Profils, on cherche l'email principal
		if (defined('_DIR_PLUGIN_PROFILS')) {
			$chercher = '%<(li|div)[^>]*email_principal[^>]*>.*?</\1>%is';
		}
		// Sinon cas par défaut, on insère après le champ email (dans le editer-groupe)
		else {
			$chercher = '%<(li|div)[^>]*(saisie|editer)_mail_inscription[^>]*>.*?</\1>%is';
		}
		
		$flux['data'] = preg_replace(
			$chercher,
			"$0$champs_password",
			$flux['data']
		);
	}
	
	return $flux;
}

/**
 * Vérifie les deux champs de mot de passe
 *
 * @param array $flux
 * @return array
 */
function inscriptionmotdepasse_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'inscription'){
		// Si les deux champs de mot de passe sont différents, ce n'est pas bien confirmé
		if (_request('password') != _request('password_confirmation')){
			$flux['data']['password_confirmation'] = _T('info_passes_identiques');
		}
		
		if ( strlen(_request('password')) < _PASS_LONGUEUR_MINI ){
			$flux['data']['password'] = _T('info_passe_trop_court_car_pluriel', array('nb' => _PASS_LONGUEUR_MINI));
		}
		
		// Mais si l'un des deux champs n'est pas rempli, cette erreur prend le dessus
		if (!_request('password')){
			$flux['data']['password'] = _T('info_obligatoire');
		}
		if (!_request('password_confirmation')){
			$flux['data']['password_confirmation'] = _T('info_obligatoire');
		}
	}
	
	if ($flux['args']['form'] == 'login'){
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'login='.sql_quote(_request('var_login')).' OR email=' .sql_quote(_request('var_login')) );
		
		if ($statut == 'nouveau'){
			$flux['data']['message_erreur'] = _T('inscriptionmotdepasse:erreur_email_non_confirme');        
		}        
	}
	
	return $flux;
}

