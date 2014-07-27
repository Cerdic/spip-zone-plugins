<?php
/**
 * Définit les autorisations du plugin Sites pour projets
 *
 * @plugin     Sites pour projets
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function projets_sites_autoriser(){}


// -----------------
// Objet projets_sites


/**
 * Autorisation de voir un élément de menu (projetssites)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssites_menu_dist($faire, $type, $id, $qui, $opt){
	return true;
} 


/**
 * Autorisation de voir le bouton d'accès rapide de création (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssitecreer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('creer', 'projets_site', '', $qui, $opt);
} 

/**
 * Autorisation de créer (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssite_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo'; 
}

/**
 * Autorisation de voir (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssite_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

/**
 * Autorisation de modifier (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssite_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}

/**
 * Autorisation de supprimer (projetssite)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_projetssite_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


/**
 * Autorisation de lier/délier l'élément (projetssites)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerprojetssites_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

?>