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
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{supprimer_commande,#ID_COMMANDE,#SELF}
 *     ```
 *
 * @uses commandes_supprimer()
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

	// suppression : on ne supprime pas de la base mais on mets le statut poubelle
	// un cron supprimera quand ca sera safe, c'est a dire qu'il existera au moins une nouvelle commande
	// pour etre sur de ne pas reutiliser ce id_commande (sous sqlite)
	if ($id_commande = intval($id_commande)) {
		spip_log("Commande $id_commande -> poubelle",'commandes');
		sql_updateq("spip_commandes",array('statut'=>'poubelle'),'id_commande = '.intval($id_commande));
	}

}

?>
