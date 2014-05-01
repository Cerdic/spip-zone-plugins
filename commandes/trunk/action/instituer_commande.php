<?php
/**
 * Fonction du plugin Commandes
 * Action : changer le statut d'une commande
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Change le statut d'une commande
 *
 * ex: #URL_ACTION_AUTEUR{instituer_commande,#ID_COMMANDE-envoye,#SELF}
 * 
 * @param $arg string
 *     arguments séparés par un charactère non alphanumérique
 *     1) id_commande : identifiant de la commande
 *     2) statut : nouveau statut
 * @return void
 */
function action_instituer_commande($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_commande, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	if ($id_commande = intval($id_commande)) {
		spip_log("action_instituer_commande id_commande=$id_commande et statut=$statut",'commandes');
		include_spip('action/editer_commande');
		commande_instituer($id_commande, array('statut' => $statut));
	}
}


?>
