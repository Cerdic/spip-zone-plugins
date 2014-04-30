<?php
/**
 * Action de suppression d'un détail d'une commande
 *
 * @plugin     Commandes
 * @copyright  2013
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprime un détail (un article) d'une commande, dans la table spip_commandes_details
 * si on est autorisé (cf. commandes_autorisations.php)
 * ex: #URL_ACTION_AUTEUR{supprimer_detail_commande,#ID_COMMANDE-#ID_COMMANDES_DETAIL,#SELF}
 * 
 * @param $arg string arguments séparés par un charactère non alphanumérique
 *                    id_commande:          identifiant de la commande
 *                    id_commandes_detail:  identifiant du détail
 * @return unknown_type
**/
function action_supprimer_detail_commande($arg=null) {

	if ($arg==null) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_commande, $id_detail) = preg_split('/[\W]/', $arg);

	if (
		$id_commande = intval($id_commande)
		and $id_detail = intval($id_detail)
		and autoriser('supprimerdetail','commande',$id_commande)
	) {
		sql_delete("spip_commandes_details", "id_commande=".$id_commande . " AND id_commandes_detail=".$id_detail);
	}

}


?>
