<?php
/**
 * Action : supprimer une adresse
 *
 * @plugin     Coordonnees
 * @copyright  2014
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Action
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer une adresse
 * arg : id_adresse
 * exemple : #URL_ACTION_AUTEUR{supprimer_adresse, #ID_ADRESSE, #SELF}
 */

function action_supprimer_adresse_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_adresse = intval($arg);

	if ($id_adresse>0 AND autoriser('supprimer', 'adresse', $id_adresse)) {
		sql_delete('spip_adresses', "id_adresse=" . sql_quote($id_adresse));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_adresse/$id_adresse'");
	}
}

?>
