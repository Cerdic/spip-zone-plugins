<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Authentification SAML
 *
 * @plugin     Authentification SAML
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\SimpleSaml\Authentification
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Identifie dans SPIP l'auteur connecté par SAML
 *
 * @param \SimpleSAML_Auth_Simple $simplesaml
 * @return bool
 *     True si réussi, false sinon.
**/
function simplesaml_auth_loger() {

	$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
	if (!$simplesaml->isAuthenticated()) {
		return false;
	}

	$nameid     = $simplesaml->getAuthData('saml:sp:NameID');
	$nameid     = is_array($nameid) ? $nameid['Value'] : $nameid->value;

	$attributes = $simplesaml->getAttributes();
	$login      = $attributes['uid'][0];
	$prenom     = $attributes['gn'][0];
	$nom        = $attributes['sn'][0];
	$email      = $attributes['email'][0];

	if ($prenom) {
		$nom = $prenom . ' ' . $nom;
	}

	if (!$nameid) {
		spip_log("No NameID found in SAML data, cancel login", 'simplesaml.' . _LOG_ERREUR);
		return false;
	}

	spip_log("Authentification reussi pour l'utilisateur = " . $email, 'simplesaml');

	// chercher notre auteur, s'il existe.
	// nameid
	$auteur = sql_fetsel('*', 'spip_auteurs', array(
		'source = ' . sql_quote('saml'),
		'statut != ' . sql_quote('5poubelle'),
		'nameid = ' . sql_quote($nameid)
	));

	// sinon uid.
	if (!$auteur) {
		$auteur = sql_fetsel('*', 'spip_auteurs', array(
			'source = ' . sql_quote('saml'),
			'statut != ' . sql_quote('5poubelle'),
			'login = ' . sql_quote($login)
		));
	}

	include_spip('action/editer_auteur');
	if (!$auteur) {
		$id_auteur = auteur_inserer('saml', array(
			'login'   => $login,
			'statut'  => '6forum',
			'nameid'  => $nameid,
			'nom'     => $nom,
			'email'   => $email
		));
		if (!$id_auteur) {
			spip_log('impossible de créer le nouvel auteur spip', 'simplesaml');
			return false;
		}
	} else {
		$id_auteur = $auteur['id_auteur'];
		// mettre à jour ses données
		sql_updateq(
			'spip_auteurs',
			array(
				'login'   => $login,
				'nameid'  => $nameid,
				'nom'     => $nom,
				'email'   => $email
			),
			'id_auteur = ' . $id_auteur
		);
	}

	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	include_spip('inc/auth');
	return auth_loger($auteur);
}



/**
 * Vider les sessions de l'auteur connecté
**/
function simplesaml_vider_sessions() {
	// le logout explicite vaut destruction de toutes les sessions
	if (isset($_COOKIE['spip_session'])) {
		include_spip('inc/cookie');
		$session = charger_fonction('session', 'inc');
		$session($GLOBALS['visiteur_session']['id_auteur']);
		spip_setcookie('spip_session', $_COOKIE['spip_session'], time()-3600);
	}
}


/**
 * Si une authentification a déjà été réalisée sur un autre sous-domaine de ce domaine,
 * on va se loger automatiquement sur le fournisseur d'identité.
 *
 * Pour savoir si c'est le cas, on teste la présence d'un cookie, cookie qui doit
 * s'appliquer sur tout le domaine, tel que '.domaine.tld'
 *
 * Le nom et la valeur du cookie sont à définir en configuration.
 *
 * @param string $url URL de retour
 * @return bool
 *     True si réussi, false sinon.
**/
function simplesaml_auth_autologer() {
	include_spip('inc/config');
	if (lire_config('simplesaml/autologin/activer')) {
		$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
		if (!$simplesaml->isAuthenticated()) {
			$cookie = lire_config('simplesaml/autologin/cookie/nom');
			$valeur = lire_config('simplesaml/autologin/cookie/valeur');
			if (isset($_COOKIE[$cookie]) and ($_COOKIE[$cookie] == $valeur)) {
				// a priori on est identifié sur le même domaine quelque part.
				$simplesaml->requireAuth();
			}
		} else {
			simplesaml_auth_loger();
			include_spip('inc/headers');
			redirige_par_entete(self());
		}
	}
}


/**
 * Déloge du SSO si on y est authentifié
 *
 * Provoque une redirection vers le SSO pour ça si besoin
 *
 * @param string $url URL de retour
**/
function simplesaml_auth_deloger($url) {
	$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
	if ($simplesaml->isAuthenticated()) {
		// attention à ne pas créer une boucle infinie si autologin est actif
		// dans ce cas, les sessions spip seront vidées automatiquement au retour
		if (!lire_config('simplesaml/autologin/activer')) {
			simplesaml_vider_sessions();
		}
		$simplesaml->logout(url_absolue($url));
		// normalement… ça doit rediriger
	}
}


/**
 * Déloge de SPIP un auteur s'il n'est plus connecté au SSO
 *
 * @param string $url URL de retour
**/
function simplesaml_auth_deloger_spip_si_besoin($url) {
	$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
	if (!$simplesaml->isAuthenticated()) {
		// aucasou on serait effectivement logé ailleurs sur le SSO
		simplesaml_auth_autologer();

		// sinon, tuer notre session SPIP
		simplesaml_vider_sessions();
		include_spip('inc/headers');
		redirige_par_entete(self());
	}
}
