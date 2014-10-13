<?php
/**
 * Action du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprime une commande et ses données associées (détails + adresses)
 *
 *     ```
 *     #URL_ACTION_AUTEUR{supprimer_commande,#ID_COMMANDE,#SELF}
 *     ```
 * 
 * @param $arg string
 *     id_commande : identifiant de la commande
 * @return void
 */
function action_supprimer_commande_dist($arg=null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$id_commande = $arg;

	// suppression
	if ($id_commande = intval($id_commande)) {
		include_spip('inc/commandes');
		commandes_supprimer($id_commande);
	}

}

?>
