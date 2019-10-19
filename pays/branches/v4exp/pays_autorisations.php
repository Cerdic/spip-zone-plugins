<?php
/**
 * Définit les autorisations du plugin Pays
 *
 * @plugin     Pays
 * @copyright  2015
 * @author     2. Cyril MARION
 * @licence    GNU/GPL
 * @package    SPIP\Pays\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function pays_autoriser(){}


// -----------------
// Objet pays

/**
 * Autorisation de voir un élément de menu (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pays_menu_dist($faire, $type, $id, $qui, $opt){
	if (!isset($GLOBALS['meta']['pays'])) return false;
	if (($config = unserialize($GLOBALS['meta']['pays']))===false) return false;
	if (!isset($config['pays_objets'])) return false;
	if (!strlen(implode("",$config['pays_objets']))) return false;

	// on a configurer le plugin pour utiliser les liens avec les pays
	// affichons donc le menu dans edition
	// (on pourrait aussi checker la presence de lien en BDD, mais dispensieux)
	return true;
} 


/**
 * Autorisation de créer (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pays_creer_dist($faire, $type, $id, $qui, $opt) {
	return false; 
}

/**
 * Autorisation de voir (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pays_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation de modifier (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pays_modifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}

/**
 * Autorisation de supprimer (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_pays_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return false;
}


/**
 * Autorisation de lier/délier l'élément (pays)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerpays_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

