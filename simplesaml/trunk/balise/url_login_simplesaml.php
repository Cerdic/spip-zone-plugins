<?php

/**
 * Ce fichier gère la balise dynamique `#URL_LOGIN_SIMPLESAML`
 *
 * @package SPIP\SimpleSaml\Balises
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Compile la balise dynamique `#URL_LOGIN_SIMPLESAML` qui génère une URL permettant
 * de connecter l'auteur actuellement déconnecté sur le SSO
 * 
 * @balise
 * @example
 *     ```
 *     [<a href="(#URL_LOGIN_SIMPLESAML)">connexion</a>]
 *     ```
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée du code compilé
**/
function balise_URL_LOGIN_SIMPLESAML ($p) {
	return calculer_balise_dynamique($p, 'URL_LOGIN_SIMPLESAML', array());
}


/**
 * Calculs de paramètres de contexte automatiques pour la balise URL_LOGIN_SIMPLESAML
 *
 * @param array $args
 *   Liste des arguments transmis à la balise
 *   - `$args[0]` = URL destination après login `[(#URL_LOGIN_SIMPLESAML{url})]`
 * @param array $context_compil
 *   Tableau d'informations sur la compilation
 * @return array
 *   Liste (url) des arguments collectés.
 */
function balise_URL_LOGIN_SIMPLESAML_stat ($args, $context_compil) {
	$url = isset($args[0]) ? $args[0] : '';
	return array($url);
}

/**
 * Exécution de la balise dynamique `#URL_LOGIN_SIMPLESAML`
 *
 * Retourne une URL de connexion uniquement si le visiteur est déconnecté.
 *
 * @param string $cible
 *     URL de destination après connexion
 * @return string
 *     URL de connexion ou chaîne vide.
**/
function balise_URL_LOGIN_SIMPLESAML_dyn($cible) {
	if (!empty($GLOBALS['visiteur_session']['login']) AND !empty($GLOBALS['visiteur_session']['statut'])) return '';
	return generer_url_action('login_simplesaml',"login=public&url=" . rawurlencode($cible ? $cible : self('&')));
}
