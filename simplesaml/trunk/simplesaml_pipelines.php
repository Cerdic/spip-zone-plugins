<?php
/**
 * Pipelines utilisés par le plugin Authentification SAML
 *
 * @plugin     Authentification SAML
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\SimpleSaml\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajoute les librairies PHP nécessaires, récupérées avec Composer.
 *
 * @param \Composer_JSON $Composer
 * @return \Composer_JSON
**/
function simplesaml_preparer_composer_json($Composer) {
	// 1.x pas encore avec une version compatible PHP7.
	$Composer->add_require("simplesamlphp/simplesamlphp", "dev-master");
	return $Composer;
}



/**
 * Sur un logout
 *
 * @param array $flux
 * @return array
**/
function simplesaml_trig_auth_trace($flux) {
	if ($flux['args']['date'] == '0000-00-00 00:00:00') {
		include_spip('inc/session');
		// si c'est l'auteur acteullement connecté qui se déconnecte
		// et qu'il venait d'une connexion saml
		if (session_get('id_auteur')
			and ($flux['args']['row']['id_auteur'] == session_get('id_auteur'))
			and (session_get('source') == 'saml'))
		{
			// lorsqu'on arrive ici depuis l'action logout, on a peut être une url
			include_spip('auth/simplesaml');
			$url = securiser_redirect_action(_request('url'));
			simplesaml_auth_deloger($url ? $url : url_de_base());
		}
	}
	return $flux;
}

/**
 * Lors d'un test de session 
 *
 * Déconnecter (logout) si on n'est plus connecté sur le SSO
 * 
 * @param string $def
 * @return string
**/
function simplesaml_definir_session($def) {
	include_spip('inc/session');

	// On a une session SPIP. Si c'est en saml, vérifier la session sur l'idp
	if (verifier_session()) {
		$source = session_get('source');
		if ($source == 'saml') {
			include_spip('auth/simplesaml');
			simplesaml_auth_deloger_spip_si_besoin(self());
		}
	} else {
		include_spip('auth/simplesaml');
		simplesaml_auth_autologer();
	}

	return $def;
}

