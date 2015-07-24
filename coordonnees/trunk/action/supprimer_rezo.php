<?php
/**
 * Action : supprimer un réseau social
 *
 * @plugin     Coordonnees
 * @copyright  2015
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Action
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer un réseau social
 * arg : id_rezo
 * exemple : #URL_ACTION_AUTEUR{supprimer_rezo, #ID_REZO, #SELF}
 */

function action_supprimer_rezo_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_rezo = intval($arg);

	if ($id_rezo>0 AND autoriser('supprimer', 'rezo', $id_rezo)) {
		sql_delete('spip_rezos', "id_rezo=" . sql_quote($id_rezo));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_rezo/$id_rezo'");
	}
}

?>
