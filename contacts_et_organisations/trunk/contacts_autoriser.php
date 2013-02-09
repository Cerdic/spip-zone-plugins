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

?>
