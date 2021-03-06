<?php
/**
 * Définit les autorisations du plugin Itinéraires
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function itineraires_autoriser(){}


// -----------------
// Objet itineraires


/**
 * Autorisation de voir un élément de menu (itineraires)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraires_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de voir le bouton d'accès rapide de création (itineraire)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itinerairecreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'itineraire', '', $qui, $opt);
} 

/**
 * Autorisation de créer (itineraire)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraire_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

/**
 * Autorisation de voir (itineraire)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraire_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (itineraire) : soit admin complet soit auteur de l’itinéraire
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraire_modifier_dist($faire, $type, $id, $qui, $opt) {
	if (
		// admin complet
		($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or
		// faire partie des auteurs
		(
			$auteurs = sql_allfetsel('id_auteur', 'spip_auteurs_liens', array('objet = "itineraire"', 'id_objet = '.intval($id)))
			and is_array($auteurs)
			and $auteurs = array_map('reset', $auteurs)
			and in_array($qui['id_auteur'], $auteurs)
		)
	){
		return true;
	}
	else{
		return false;
	}
}

/**
 * Autorisation de supprimer (itineraire)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraire_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// -----------------
// Objet itineraires_etapes


/**
 * Autorisation de créer une étape dans un itinéraire : pouvoir modifier l'itinéraire
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itineraire_creeretapedans_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'itineraire', $id, $qui, $opt);
}

/**
 * Autorisation de créer une étape : il faut avoir un contexte d'itinéraire
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itinerairesetape_creer_dist($faire, $type, $id, $qui, $opt) {
	if (isset($opt['id_itineraire']) and $id_itineraire =intval($opt['id_itineraire'])) {
		return autoriser('creeretapedans', 'itineraire', $id_itineraire, $qui, $opt);
	}
	else {
		return false;
	}
}

/**
 * Autorisation de voir (itinerairesetape)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itinerairesetape_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier une étape : pouvoir modifier l'itinéraire parent
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itinerairesetape_modifier_dist($faire, $type, $id, $qui, $opt) {
	if (isset($opt['id_itineraire'])) {
		$id_itineraire = $opt['id_itineraire'];
	}
	else {
		$id_itineraire = sql_getfetsel(
			'id_itineraire',
			'spip_itineraires_etapes',
			'id_itineraires_etape = '.intval($id)
		);
	}
	
	return autoriser('modifier', 'itineraire', $id_itineraire, $qui, $opt);
}

/**
 * Autorisation de supprimer (itinerairesetape)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_itinerairesetape_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
