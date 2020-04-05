<?php
/**
 * Fonctions utiles au plugin Connection
 *
 * @plugin     Connection
 * @copyright  2016
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Connecteur\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Balise des connecteurs
 *
 * Active le lien de connection spécifique à un connecteur
 *
 * ```
 * #CONNECTEUR_FACEBOOK
 * ```
 * Cette balise appel une fonction du dossier connecteur: `connecteur_facebook_lien`
 *
 * @param mixed $p
 * @access public
 * @return mixed
 */
function balise_CONNECTEUR__dist($p) {

	// Récupérer le type de connecteur
	// Le substr supprime la partie "CONNECTEUR_" pour ne garder que la source
	$connecteur_type = strtolower(substr($p->nom_champ, 11));
	$redirect = interprete_argument_balise(1, $p);

	// Toujours avoir au moins une redirection
	if (empty($redirect)) {
		$redirect = '\''.self().'\'';
	}

	$site = interprete_argument_balise(2, $p);
	if ($site) {
		$site = ", 'true'";
	} else {
		$site = '';
	}

	$p->code = "connecteur_lien('$connecteur_type', $redirect $site)";
	$p->interdire_scripts = false;

	return $p;
}

/**
 * Charger la fonction du service
 * Utiliser charger_fonction dans une fonction balise provoque des bugs
 *
 * @access public
 */
function connecteur_lien($source, $redirect = '', $site = false) {
	include_spip('inc/session');
	// On appel la fonction du service
	if ($site) {
		// On a explicitement demandé de connecter un compte pour le site
		$action = generer_action_auteur('connexion_site', $source, $redirect, true);
	} elseif (!empty(session_get('id_auteur'))) {
		// Si une session SPIP est déjà ouverte, on va ajouter le compte
		$action = generer_action_auteur('connecteur_lier', $source, $redirect, true);
	} else {
		// On est dans la création d'un nouveau compte SPIP
		$action = generer_action_auteur('connexion', $source, $redirect, true);
	}

	$f = charger_fonction($source.'_lien', 'connecteur');
	return $f($action);
}

/**
 * Cette fonction va créer un auteur SPIP en fonction d'un tableau
 * de donnée simple
 *
 * ```
 * array('nom' => 'truc', 'email' => 'truc@machin.be')
 * ```
 *
 * @param mixed $source
 * @access public
 */
function connecteur_creer_auteur($info, $statut = '6forum') {

	// Inscrire l'auteur sur base des informations du connecteur
	$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
	$desc = $inscrire_auteur(
		$statut,
		$info['email'],
		$info['nom']
	);

	return $desc;
}

/**
 * Une fonction qui va renvoyer les informations complète d'un auteur
 * sur base de son email
 *
 * @param array $info Tableau contenant une clé email
 * @access public
 * @return array Les informations complète de l'auteur
 */
function connecteur_completer_auteur($info) {
	// On complète le profil de l'auteur afin de pouvoir le connecteur
	$info = sql_fetsel(
		'*',
		'spip_auteurs',
		array(
			'email='.sql_quote($info['email']),
			'statut !='.sql_quote('5poubelle')
		)
	);

	return $info;
}

/**
 * Connecter un auteur à SPIP
 *
 * @param array $auteur_info Tableau contenant une clé email
 * @access public
 */
function connecteur_connecter($auteur_info) {
	// Récupérer toute les informations de l'auteur
	$auteur_info = connecteur_completer_auteur($auteur_info);
	include_spip('inc/auth');
	auth_loger($auteur_info);

	return $auteur_info;
}

/**
 * Balise TOKEN
 * Permet de récupérer le token de l'utilisateur connecté.
 *
 * ```
 * #TOKEN_FACEBOOK
 * ```
 *
 * @param mixed $p
 * @access public
 * @return mixed
 */
include_spip('inc/token');
function balise_TOKEN__dist($p) {

	$id_auteur = interprete_argument_balise(1, $p);

	if (!isset($id_auteur)) {
		$id_auteur = session_get('id_auteur') ;
	} else {
		$id_auteur = intval($id_auteur);
	}

	$connecteur_type = strtolower(substr($p->nom_champ, 6));

	$p->code = "connecteur_get_token($id_auteur, '$connecteur_type')";
	$p->interdire_scripts = false;

	return $p;
}
