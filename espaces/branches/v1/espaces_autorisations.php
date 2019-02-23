<?php
/**
 * Définit les autorisations du plugin Espaces
 *
 * @plugin     Espaces
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Espaces\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
  return;
}


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function espaces_autoriser() {
}


/* Exemple
function autoriser_espaces_configurer_dist($faire, $type, $id, $qui, $opt) {
  // type est un objet (la plupart du temps) ou une chose.
  // autoriser('configurer', '_espaces') => $type = 'espaces'
  // au choix :
  return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
  return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
  return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
  // ...
}
*/

// -----------------
// Objet espaces


/**
 * Autorisation de voir un élément de menu (espaces)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espaces_menu_dist($faire, $type, $id, $qui, $opt) {
  return true;
}


/**
 * Autorisation de créer (espace)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espace_creer_dist($faire, $type, $id, $qui, $opt) {
  return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de voir (espace)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espace_voir_dist($faire, $type, $id, $qui, $opt) {
  return true;
}

/**
 * Autorisation de modifier (espace)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espace_modifier_dist($faire, $type, $id, $qui, $opt) {
  return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de supprimer (espace)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_espace_supprimer_dist($faire, $type, $id, $qui, $opt) {
  return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de lier/délier l'élément (espaces)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerespaces_dist($faire, $type, $id, $qui, $opt) {
  return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}
