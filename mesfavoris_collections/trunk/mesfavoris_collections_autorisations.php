<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function mesfavoris_collections_autoriser(){}


// -----------------
// Objet favoris_collections

/**
 * Autorisation de créer (favoris_collection) : tout utilisateur peut créer ses collections
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_favoriscollection_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['id_auteur'] > 0; 
}

/**
 * Autorisation de voir (favoris_collection)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_favoriscollection_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (favoris_collection) : soit admin complet soit auteur de la collection
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_favoriscollection_modifier_dist($faire, $type, $id, $qui, $opt) {
	if (
		// admin complet
		($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or
		// est auteur
		(
			$id_auteur = sql_getfetsel('id_auteur', 'spip_favoris_collections', array('id_favoris_collection = '.intval($id)))
			and $qui['id_auteur'] == $id_auteur
		)
	){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Autorisation de supprimer (favoris_collection) : pouvoir modifier
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_favoriscollection_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'favoris_collection', $id, $qui, $opt);
}
