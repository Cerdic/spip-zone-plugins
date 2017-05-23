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
 * Abandonne une commande liee a une transaction (appel conventionnel par abandonner_transaction)
 *
 * @uses action_supprimer_commande_dist()
 *
 * @param $arg string
 *     id_commande : identifiant de la commande
 * @return void
 */
function action_abandonner_commande_dist($arg=null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$id_commande = $arg;

	// pas de statut abandon pour le moment, on passe en poubelle donc
	$supprimer_commande = charger_fonction('supprimer_commande','action');
	$supprimer_commande($id_commande);

}

