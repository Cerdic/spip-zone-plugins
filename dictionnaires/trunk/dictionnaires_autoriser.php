<?php

/**
 * Définit les autorisations du plugin dictionnaires 
 *
 * @package SPIP\Dictionnaires\Autorisations
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function dictionnaires_autoriser(){}

/**
 * Autorisation de voir le bouton de dictionnaires du menu d'édition
 *
 * Tout le monde
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_dictionnaires_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
}

/**
 * Autorisation de voir le bouton d'ajout rapide de définition
 *
 * Tous les rédacteurs, lorsqu'il y a un dictionnaire
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_definition_creer_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'definition', $id, $qui, $opt);
}

/**
 * Autorisation de créer un dictionnaire 
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
function autoriser_dictionnaire_creer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer', $type, $id, $qui, $opt);
}


/**
 * Autorisation de modifier un dictionnaire
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
function autoriser_dictionnaire_modifier_dist($faire, $type, $id, $qui, $opt){
	return autoriser('configurer', $type, $id, $qui, $opt);
}

/**
 * Autorisation de dupprimer un dictionnaire
 *
 * Ceux qui peuvent configurer le site,
 * s'il n'y a plus aucune définition dedans !
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_dictionnaire_supprimer_dist($faire, $type, $id, $qui, $opt){
	return ($id > 0)
		and autoriser('configurer', $type, $id, $qui, $opt)
		and !sql_fetsel(
			'id_definition',
			'spip_definitions',
			array(
				'id_dictionnaire = '.$id,
				sql_in('statut', array('publie', 'prop'))
			)
		);
}


/**
 * Autorisation de créer une définition dans un dictionnaire 
 *
 * Les rédacteurs
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_dictionnaire_creerdefinitiondans_dist($faire, $type, $id, $qui, $opt){
	return $qui['statut'] <= '1comite';
}

/**
 * Autorisation de créer une définition 
 *
 * Les rédacteurs, et au moins un dictionnaire
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_definition_creer_dist($faire, $type, $id, $qui, $opt){
	return $qui['statut'] <= '1comite' and sql_countsel('spip_dictionnaires');
}


/**
 * Autorisation de modifier une définition
 *
 * Un rédacteur si pas publié, un admin sinon
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_definition_modifier_dist($faire, $type, $id, $qui, $opt){
	if ($id > 0
		and $statut = sql_getfetsel('statut', 'spip_definitions', 'id_definition = '.$id)
		and (
			($statut == 'publie' and $qui['statut'] <= '0minirezo')
			or
			($statut != 'publie' and $qui['statut'] <= '1comite')
		)
	){
		return true;
	}
	
	return false;
}


/**
 * Autorisation de supprimer une définition
 *
 * Pareil que pour modifier une définition
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_definition_supprimer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier', $type, $id, $qui, $opt);
}

?>
