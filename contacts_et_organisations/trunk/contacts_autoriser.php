<?php
/**
 * Définit les autorisations du plugin Contacts & Organisations 
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Autorisations
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function contacts_autoriser(){}

/**
 * Autorisation de modifier une organisation
 *
 * Seuls les admins et l'auteur lié s'il existe peuvent modifier l'organisation.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_organisation_modifier_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer')
		or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_organisations', 'id_organisation = '.intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

/**
 * Autorisation de modifier un contact
 *
 * Seuls les admins et l'auteur lié s'il existe peuvent modifier le contact
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_contact_modifier_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer')
		or (
			$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact = '.intval($id))
			and $id_auteur > 0
			and $id_auteur == $qui['id_auteur']
		);
}

/**
 * Autorisation de créer un annuaire
 *
 * Ceux qui peuvent configurer le site
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_annuaire_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer', $type, $id, $qui, $opt);
}

/**
 * Autorisation de modifier un annuaire
 *
 * Ceux qui peuvent configurer le site
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_annuaire_modifier_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer', $type, $id, $qui, $opt);
}

/**
 * Autorisation de supprimer un annuaire
 *
 * Ceux qui peuvent configurer le site ET qu'il n'y ait rien dans l'annuaire
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_annuaire_supprimer_dist($faire, $type, $id, $qui, $opt){
	return (
		autoriser('configurer', $type, $id, $qui, $opt)
		and !sql_count(sql_select('id_organisation', 'spip_organisations', 'id_annuaire = '.intval($id)))
		and !sql_count(sql_select('id_contact', 'spip_contacts', 'id_annuaire = '.intval($id)))
	);
}

?>
