<?php
/**
 * Définit les autorisations du plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function identifiants_autoriser() {
}


/**
 * Autorisation à voir les identifiants.
 *
 * Uniquement les webmestres.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_identifiant_voir_dist($faire, $type, $id, $qui, $opts) {
	$autoriser = autoriser('webmestre', '', '', $qui);
	return $autoriser;
}


/**
 * Autorisation à modifier les identifiants.
 *
 * Uniquement les webmestres.
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_identifiant_modifier_dist($faire, $type, $id, $qui, $opts) {
	$autoriser = autoriser('webmestre', '', '', $qui);
	return $autoriser;
}
