<?php
/**
 * Définit les autorisations du plugin Emplois
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function emplois_autoriser(){}


// -----------------
// Objet offres


/**
 * Autorisation de voir un élément de menu (offres)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_offres_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de créer (offre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_offre_creer_dist($faire, $type, $id, $qui, $opt) {
	return (true AND sql_countsel('spip_rubriques')>0); 
}

/**
 * Autorisation de voir (offre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_offre_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (offre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_offre_modifier_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de supprimer (offre)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_offre_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

/**
 * Autorisation de créer l'élément (offre) dans une rubrique
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rubrique_creeroffredans_dist($faire, $type, $id, $qui, $opt) {
	$offres_actif = lire_config('emplois/offres/activer_offres');
	$offres_actif == 'oui' ? $offres_actif = true : $offres_actif = false;

	return
		$offres_actif
		AND ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','offre', $id));
}

// -----------------
// Objet cvs


/**
 * Autorisation de voir un élément de menu (cvs)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_cvs_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de créer (cv)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_cv_creer_dist($faire, $type, $id, $qui, $opt) {
	return (true AND sql_countsel('spip_rubriques')>0); 
}

/**
 * Autorisation de voir (cv)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_cv_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (cv)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_cv_modifier_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de supprimer (cv)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_cv_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

/**
 * Autorisation de créer l'élément (cv) dans une rubrique
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rubrique_creercvdans_dist($faire, $type, $id, $qui, $opt) {
	$cv_actif = lire_config('emplois/offres/activer_cv');
	$cv_actif == 'oui' ? $cv_actif = true : $cv_actif = false;
	return 
		$cv_actif
		AND ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','cv', $id));
}

/* Compatibilité LIM */

if (!function_exists('autoriser_rubrique_creeroffredans')) {
	function autoriser_rubrique_creeroffredans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/offre');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$lim_rub
			AND autoriser_rubrique_creeroffredans_dist($faire, $type, $id, $qui, $opt);
	}
}

if (!function_exists('autoriser_rubrique_creercvdans')) {
	function autoriser_rubrique_creercvdans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/cv');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$lim_rub
			AND autoriser_rubrique_creercvdans_dist($faire, $type, $id, $qui, $opt);
	}
}