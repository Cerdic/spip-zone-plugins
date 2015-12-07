<?php

/**
 * Action pour connecter une personne non authentifiée en utilisant le SSO
 *
 * @package SPIP\SimpleSaml\Authentification
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/cookie');

/**
 * Se loger
 * 
 * Redirige sur l'établissement fournisseur d'identité
 */
function action_login_simplesaml_dist()
{
	$login =_request('login');
	$url = securiser_redirect_action(_request('url'));

	if (!$url) {
		$url = url_de_base();
	}

	$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
	if (!$simplesaml->isAuthenticated()) {
		$simplesaml->requireAuth(); // redirige sur l'authentification SSO
	}

	// Arrivé ici, je suis authentifié :)
	include_spip('auth/simplesaml');
	simplesaml_auth_loger();

	include_spip('inc/headers');
	redirige_par_entete(parametre_url($url, 'var_hasard', uniqid(rand()), '&'));
}

