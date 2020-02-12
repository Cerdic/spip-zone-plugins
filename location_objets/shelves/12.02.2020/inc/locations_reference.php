<?php
/**
 * Fonctions du plugin Commandes relatives à la référence de commande
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Pioché dans Commandes.
 * Génère un numéro unique utilisé pour remplir le champ `reference` lors de la création d'une location.
 *
 * Le numéro retourné est la date suivi de l'identifiant
 *
 * @example
 *     ```
 *     $fonction_reference = charger_fonction('locations_reference', 'inc/');
 *     ```
 *
 * @param int $id_objets_location

 * @return string
 *     reference de la commande
**/
function inc_locations_reference_dist($id_objets_location){

	if ($date = sql_getfetsel('date', 'spip_objets_locations', 'id_objets_location=' . intval($id_objets_location))) {
		$t = strtotime($date);
	}
	else {
		$t = $_SERVER['REQUEST_TIME'];
	}

	// format YYYYMMDDNNNNNN
	$reference = date('Ymd', $t) . str_pad(intval($id_objets_location), 6, '0', STR_PAD_LEFT);

	return $reference;
}
