<?php
/**
 * Gestion du formulaire de profil des utilisateurs
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/profils');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return string
 *     Hash du formulaire
 */
function formulaires_profil_identifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	return serialize(array(intval($id_auteur)));
}

/**
 * Saisies du formulaire de profil
 *
 * Déclarer les saisies utilisées pour générer le formulaire.
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return array
 *     Environnement du formulaire
 */
function formulaires_profil_saisies_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	$saisies = profils_chercher_saisies_profil('edition', $id_auteur, $id_ou_identifiant_profil);
	
	return $saisies;
}

/**
 * Chargement du formulaire de profil
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return array
 *     Environnement du formulaire
 */
function formulaires_profil_charger_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	include_spip('inc/autoriser');
	$contexte = array();
	
	// Si pas d'id_auteur on prend celui connecté actuellement
	if (!intval($id_auteur)) {
		$id_auteur = session_get('id_auteur');
	}
	
	// On vérifie que l'auteur existe et qu'on a le droit de le modifier
	if (
		!$auteur = sql_fetsel('id_auteur,nom,email', 'spip_auteurs', 'id_auteur = '.intval($id_auteur))
		or !$id_auteur = intval($auteur['id_auteur'])
		or (!($id_auteur == session_get('id_auteur')) and !autoriser('modifier', 'auteur', $id_auteur))
	) {
		return array(
			'editable' => false,
			'message_erreur' => _T('profils:erreur_autoriser_profil'),
		);
	}
	
	// Récupérer toutes les infos possibles déjà existantes
	$infos = profils_recuperer_infos($id_auteur, $id_ou_identifiant_profil);
	
	// On remplit le contexte avec ces informations (et un préfixe pour le contact)
	$contexte = array_merge($contexte, $infos);
	
	//var_dump($contexte);
	return $contexte;
}

/**
 * Vérifications du formulaire de profil
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return array
 *     Tableau des erreurs
 */
function formulaires_profil_verifier_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	$erreurs = array();
	
	return $erreurs;
}

/**
 * Traitement du formulaire de profil
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_auteur
 *     Identifiant du compte utilistateur. 'new' pour une création.
 * @param int|string $id_ou_identifiant_profil
 *     ID SQL ou identifiant textuel du profil voulu
 * @return array
 *     Retours des traitements
 */
function formulaires_profil_traiter_dist($id_auteur = 'new', $id_ou_identifiant_profil = '') {
	//$retours = formulaires_editer_objet_traiter('profil', $id_profil, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	return $retours;
}
