<?php
/**
 * Définit les autorisations du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function info_sites_autoriser() {
}

/**
 * Autorisation d'accès è l'espace privé ?
 * Surcharge de autoriser_ecrire_dist() > ecrire/inc/autoriser.php
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_ecrire($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

// *****************************
// Autorisation par défaut
// *****************************

/**
 * Autorisation de créer
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositescreer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de voir
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de modifier
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de supprimer
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositessupprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}


/**
 * Autorisation de mise à jour
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesmaj_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation d'association
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

// *****************************
// Les sites de projets (projetssite)
// *****************************

/**
 * Autorisation de créer (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositescreer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de voir (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	echo "<pre>";
	var_dump($qui);
	echo "</pre>";
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de modifier (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de supprimer (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositessupprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}


/**
 * Autorisation de mise à jour (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositesmaj_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation d'association (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssites_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de voir les données sensibles (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssitesecurite_voir($faire, $type, $id, $qui, $opt) {
	include_spip('base/abstract_sql');
	$auteurs = sql_fetsel("role", "spip_auteurs_liens",
		"objet='projet' AND id_objet IN (SELECT id_objet FROM spip_projets_sites_liens WHERE objet='projet' AND id_projets_site=" . $id . ") AND id_auteur=" . $qui['id_auteur']);

	if (isset($auteurs['role'])) {
		// Pour le moment, quelque soit le rôle de l'auteur, il peut voir les éléments sécurisés
		// En effet, l'auteur a été ajouté au projet du site, donc, il fait parti de l'équipe.
		return true;
	}

	// Si l'auteur ne fait pas parti de l'équipe, on prend en compte son statut.
	return $qui['statut'] == '0minirezo';
}

// *****************************
// Les projets
// *****************************

/**
 * Autorisation de créer (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositescreer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de voir (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation de supprimer (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositessupprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de mise à jour (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositesmaj_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

/**
 * Autorisation d'association (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projets_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

