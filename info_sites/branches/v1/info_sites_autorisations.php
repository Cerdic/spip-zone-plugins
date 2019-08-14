<?php
/**
 * Définit les autorisations du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/autoriser');

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_ecrire($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de créer un contenu
 *
 * Accordée par defaut ceux qui accèdent à l'espace privé,
 * peut-être surchargée au cas par cas
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', $type, $id, $qui, $opt);
}

/**
 * Autorisation de joindre un document
 *
 * On ne peut joindre un document qu'a un objet qu'on a le droit d'editer
 * mais il faut prevoir le cas d'une *creation* par un redacteur, qui correspond
 * au hack id_objet = 0-id_auteur
 *
 * Il faut aussi que les documents aient ete actives sur les objets concernes
 * ou que ce soit un article, sur lequel on peut toujours uploader des images
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_joindredocument($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');

	// objet autorisé en upload ?
	if ($type == 'article' or in_array(table_objet_sql($type), explode(',', lire_config('documents_objets', '')))) {
		// sur un objet existant
		if ($id > 0) {
			/* On indique directement l'utilisation de l'autorisation infositesmodifier
			 * pour aller plus vite sur le formulaire d'ajout de document.
			 * Il faudrait voir peut-être par la suite s'il n'y a pas une autre source de lenteur.
			 */
			return autoriser('infositesmodifier', $type, $id, $qui, $opt);
		} // sur un nouvel objet
		elseif ($id < 0 and (abs($id) == $qui['id_auteur'])) {
			return autoriser('ecrire', $type, $id, $qui, $opt);
		}
	}

	return false;
}

// *****************************
// Autorisation par défaut
// *****************************

/**
 * Autorisation de créer
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {

	if ($type === 'auteur') {
		include_spip('inc/autoriser');

		return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

	return in_array($qui['statut'], array(
		'0minirezo',
		'1comite',
	));
}

// *****************************
// Auteurs
// *****************************

/**
 * Autorisation d'association (auteurs)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_auteurs_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositescreer_dist($faire, $type, $id, $qui, $opt) {

	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositessupprimer_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de mise à jour (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssite_infositesmaj_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetssites_infositesassocier_dist($faire, $type, $id, $qui, $opt) {
	if (is_array($opt) and isset($opt['projet'])) {
		$confirm = confirmer_roles_auteurs_projets($qui, $opt['projet']);

		return $confirm;
	}

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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'projet', $id, $qui, $opt);
}

/**
 * Surcharge d'autorisation de modifier (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'projet', $id, $qui, $opt);
}

/**
 * Autorisation de créer (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projet_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {

	if (in_array($qui['statut'], array('0minirezo',))) {
		return true;
	}

	return confirmer_roles_auteurs_projets($qui, $id);
}

/**
 * Autorisation de supprimer (projet)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * Autorisation de voir (projetscadre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetscadre_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Surcharge d'autorisation d'association (projetscadres)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
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
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'contact', $id, $qui, $opt);
}

/**
 * Autorisation de modifier une contact (infositesmodifier)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_infositesmodifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array(
			'0minirezo',
			'1comite',
		)) or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact = ' . intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

/**
 * Autorisation de suppression d'une contact (infositessupprimer)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_infositessupprimer_dist($faire, $type, $id, $qui, $opt) {
	// On prend les statuts par défaut
	return in_array($qui['statut'], array(
			'0minirezo',
			'1comite',
		)) or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact = ' . intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

/**
 * Autorisation de voir (contact)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_contact_infositesvoir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// *****************************
// Les coordonnées
// *****************************


// --------------
// Objet Adresses

/**
 * Autorisation de creer (adresse)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_adresse_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'adresse', $id, $qui, $opt);
}

/**
 * Autorisation de voir (adresse)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_adresse_voir($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesvoir', 'adresse', $id, $qui, $opt);
}

/**
 * Autorisation de modifier (adresse)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_adresse_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'adresse', $id, $qui, $opt);
}

/**
 * Autorisation de supprimer (adresse)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_adresse_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'adresse', $id, $qui, $opt);
}

/**
 * Autorisation d'associer (adresse)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associeradresses($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'adresses', $id, $qui, $opt);
}

// --------------
// Objet numeros

/**
 * Autorisation de creer (numéro)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_numero_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'numero', $id, $qui, $opt);
}

/**
 * Autorisation de voir (numéro)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_numero_voir($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesvoir', 'numero', $id, $qui, $opt);
}

/**
 * Autorisation de modifier (numéro)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_numero_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'numero', $id, $qui, $opt);
}

/**
 * Autorisation de supprimer (numéro)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_numero_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'numero', $id, $qui, $opt);
}

/**
 * Autorisation d'associer (numéro)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associernumeros($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'numeros', $id, $qui, $opt);
}


// ------------
// Objet emails

/**
 * Autorisation de creer (email)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_email_creer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'email', $id, $qui, $opt);
}

/**
 * Autorisation de voir (email)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_email_voir($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesvoir', 'email', $id, $qui, $opt);
}

/**
 * Autorisation de modifier (email)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_email_modifier($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'email', $id, $qui, $opt);
}

/**
 * Autorisation de supprimer (email)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_email_supprimer($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'email', $id, $qui, $opt);
}


/**
 * Autorisation d'associer (email)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associeremails($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'emails', $id, $qui, $opt);
}


/**
 * Récupérer les rôles d'un auteur sur un projet et ainsi s'avoir s'il a droit à certaines actions.
 *
 * @param array $qui
 * @param int $id_projet
 * @param array $role_creation
 *
 * @return bool|array
 */
function confirmer_roles_auteurs_projets($qui, $id_projet = 0, $role_creation = array()) {
	include_spip('base/abstract_sql');
	$roles = array();
	$auteur_roles = sql_allfetsel('role', 'spip_auteurs_liens',
		'objet=' . sql_quote('projet') . ' AND id_auteur=' . $qui['id_auteur'] . ' AND id_objet=' . $id_projet);
	if (is_array($auteur_roles) and count($auteur_roles) > 0) {
		foreach ($auteur_roles as $auteur_role) {
			$roles[] = $auteur_role['role'];
		}
	}
	// Liste des rôles pouvant créer des sites sur un projet.
	if (is_string($role_creation)) {
		// Si on passe un string en 3ème paramètre le rôle tel que 'developpeur', on le reformate en tableau.
		// Pas de séparateur/serialize prévu pour le moment.
		$role_creation = array($role_creation);
	}
	// On revoit le contenu du tableau $role_creation par sécu
	if (is_array($role_creation) and count($role_creation) == 0) {
		// par défaut
		$role_creation = array(
			'dir_projets',
			'chef_projets',
			'ref_tech',
			'architecte',
			'lead_developpeur',
			'developpeur',
		);
	}
	// Si on est administrateur, pas de soucis.
	if (in_array($qui['statut'], array('0minirezo'))) {
		return true;
	}
	$roles_autorises = array_intersect($roles, $role_creation);
	// roles_autorises est toujours un array et s'il est vide,
	// c'est que l'auteur n'a pas le rôle adéquate.
	if (is_array($roles_autorises) and count($roles_autorises) > 0) {
		return true;
	}

	// On prend les statuts par défaut
	return false;
}


// -----------------
// Objet projets_references

/**
 * Autorisation de créer (projetsreference)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetsreference_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('infositescreer', 'projetsreference', $id, $qui, $opt);
}

/**
 * Autorisation de voir (projetsreference)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetsreference_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesvoir', 'projetsreference', $id, $qui, $opt);
}

/**
 * Autorisation de modifier (projetsreference)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetsreference_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesmodifier', 'projetsreference', $id, $qui, $opt);
}

/**
 * Autorisation de supprimer (projetsreference)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetsreference_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('infositessupprimer', 'projetsreference', $id, $qui, $opt);
}



/**
 * Autorisation de lier/délier l'élément (projetsreferences)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_associerprojetsreferences_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('infositesassocier', 'projetsreferences', $id, $qui, $opt);

}


/**
 * Autorisation d'iconifier un auteur (mettre un logo)
 *
 * Il faut un administrateur ou que l'auteur soit celui qui demande l'autorisation
 *
 * @param  string $faire Action demandée
 * @param  string $type Type d'objet sur lequel appliquer l'action
 * @param  int $id Identifiant de l'objet
 * @param  array $qui Description de l'auteur demandant l'autorisation
 * @param  array $opt Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_projetsreference_iconifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}