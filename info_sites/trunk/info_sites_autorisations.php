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
 * Autorisation de changer le statut
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesinstituer_dist($faire, $type, $id, $qui, $opt) {
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
 * Surcharge d'autorisation de créer (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'projetssite', $id, $qui, $opt);
}

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
	return true;
}

/**
 * Surcharge d'autorisation de modifier (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'projetssite', $id, $qui, $opt);
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
 * Surcharge d'autorisation d'association (projetssites)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerprojets_sites($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'projetssites', $id, $qui, $opt);
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
 * Surcharge d'autorisation de créer (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'projet', $id, $qui, $opt);
}

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
 * Surcharge d'autorisation d'association (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerprojets($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'projets', $id, $qui, $opt);
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

// *****************************
// Les cadres de projets
// *****************************

/**
 * Surcharge d'autorisation d'association (projetscadres)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerprojets_cadres($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'projetscadres', $id, $qui, $opt);
}

/**
 * Autorisation de creer un cadre de projet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetscadre_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'projetscadre', $id, $qui, $opt);
}

/**
 * Autorisation de modifier une cadre de projet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetscadre_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'projetscadre', $id, $qui, $opt);
}

/**
 * Autorisation de suppression d'un cadre de projet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetscadre_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'projetscadre', $id, $qui, $opt);
}

// *****************************
// Les organisations
// *****************************

/**
 * Surcharge d'autorisation d'association (organisations)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerorganisations($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'organisations', $id, $qui, $opt);
}

/**
 * Autorisation de creer une organisation
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_organisation_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'organisation', $id, $qui, $opt);
}

/**
 * Autorisation de modifier une organisation
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_organisation_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'organisation', $id, $qui, $opt);
}

/**
 * Autorisation de suppression d'une organisation
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_organisation_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'organisation', $id, $qui, $opt);
}

/**
 * Autorisation de voir (organisation)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_organisation_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de voir (organisations)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_organisations_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// *****************************
// Les contacts
// *****************************

/**
 * Surcharge d'autorisation d'association (contact)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associercontacts($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'contacts', $id, $qui, $opt);
}

/**
 * Autorisation de creer une contact
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'contact', $id, $qui, $opt);
}

/**
 * Autorisation de modifier une contact
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'contact', $id, $qui, $opt);
}

/**
 * Autorisation de suppression d'une contact
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'contact', $id, $qui, $opt);
}

/**
 * Autorisation de voir (contact)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

