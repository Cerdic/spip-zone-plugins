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
	if ($flux['args']['form'] == 'inscription'){
		$flux['data']['password'] = '';
		$flux['data']['password_confirmation'] = '';
	}

    if ($flux['args']['form'] == 'login' and $GLOBALS["visiteur_session"] and $GLOBALS["visiteur_session"]['statut'] == 'nouveau'){
        $flux['data']['_deja_loge'] = '';
        $flux['data']['editable'] = ' ';
        $flux['data']['var_login'] = 'erreur';
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
		
		$flux['data'] = preg_replace(
			'%<(li|div)[^>]*saisie_mail_inscription[^>]*>.*?</\1>%is',
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
		
		// Mais si l'un des deux champs n'est pas rempli, cette erreur prend le dessus
		if (!_request('password')){
			$flux['data']['password'] = _T('info_obligatoire');
		}
		if (!_request('password_confirmation')){
			$flux['data']['password_confirmation'] = _T('info_obligatoire');
		}
	}
	return $flux;
}

/**
 * Interdit la connexion à un auteur avec le statut "nouveau"
 *
 * @param array $flux
 * @return array
 */
function inscriptionmotdepasse_formulaire_traiter($flux){
    if ($flux['args']['form'] == 'login' and $GLOBALS["visiteur_session"]['statut'] == 'nouveau'){
        supprimer_sessions($GLOBALS["visiteur_session"]["id_auteur"], true, true);
        return array('message_erreur' => _T('inscriptionmotdepasse:erreur_email_non_confirme'));
    }
    return $flux;
}
