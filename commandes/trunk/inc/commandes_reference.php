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
 * @param string $id_auteur
 *     (inutilisé) identifiant de l'auteur
 * @return int
 *     Nombre de secondes écoulées depuis le 1er janvier 1970
**/
function inc_commandes_reference_dist($id_auteur=0){
	return time();
}

?>
