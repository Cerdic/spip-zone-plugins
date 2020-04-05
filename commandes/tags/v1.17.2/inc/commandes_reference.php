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
 * Génère un numéro unique utilisé pour remplir le champ `reference` lors de la création d'une commande.
 * 
 * Le numéro retourné est le nombre de secondes écoulées depuis le 1er janvier 1970
 *
 * @example
 *     ```
 *     $fonction_reference = charger_fonction('commandes_reference', 'inc/');
 *     ```
 *
 * @param int $id_commande
 * @param int $id_auteur
 *     (optionnel) identifiant de l'auteur
 * @return string
 *     reference de la commande
**/
function inc_commandes_reference_dist($id_commande, $id_auteur=0){

	if ($date = sql_getfetsel('date', 'spip_commandes', 'id_commande='.intval($id_commande))) {
		$t = strtotime($date);
	}
	else {
		$t = $_SERVER['REQUEST_TIME'];
	}

	// format YYYYMMDDNNNNNN
	$reference = date('Ymd', $t) . str_pad(intval($id_commande), 6, '0', STR_PAD_LEFT);

	return $reference;
}
