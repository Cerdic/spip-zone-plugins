<?php
/**
 * Fonction du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Autorisations
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Remplit le champ 'id_auteur' d'une commande
 * 
 * @param int $id_commande
 *     identifiant de la commande
 * @param int $id_auteur
 *     identifiant de l'auteur
 * @return mixed|string $err
 *     Message d'erreur éventuel
**/
function inc_lier_commande_auteur_dist($id_commande,$id_auteur) {

	if (
		$id_commande = intval($id_commande)
		and $id_auteur = intval($id_auteur)
	) {
		include_spip('action/editer_commande');
		$res = commande_modifier($id_commande,array('id_auteur'=>$id_auteur));
	}
	return $res;
}
?>
