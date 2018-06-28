<?php
/**
 * Définit les autorisations du plugin Prix Objets
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Prix_objets\Autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function prix_objets_autoriser(){}

// declarations d'autorisations

/**
 * Autorisation de modifier (prix_objes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_prix_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
