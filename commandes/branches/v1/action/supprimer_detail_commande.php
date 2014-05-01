<?php
/**
 * Fonction du plugin Commandes
 * Action : suppression d'un détail d'une commande
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprime un détail d'une commande, dans la table spip_commandes_details
 *
 * ex: #URL_ACTION_AUTEUR{supprimer_detail_commande,#ID_COMMANDE-#ID_COMMANDES_DETAIL,#SELF}
 * 
 * @param $arg string
 *     arguments séparés par un charactère non alphanumérique
 *     1) id_commande : identifiant de la commande
 *     2) id_commandes_details : identifiant du détail
 * @return void
 */
function action_supprimer_detail_commande($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_commande, $id_detail) = preg_split('/[\W]/', $arg);

	if (
		$id_commande = intval($id_commande)
		and $id_detail = intval($id_detail)
	) {
		include_spip('inc/commandes');
		commandes_supprimer_detail($id_commande,$id_detail);
	}

}

?>
