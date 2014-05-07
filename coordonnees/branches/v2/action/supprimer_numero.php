<?php
/**
 * Action : supprimer une numero
 *
 * @plugin     Coordonnees
 * @copyright  2014
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Action
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer un numero
 * arg : id_numero
 * exemple : #URL_ACTION_AUTEUR{supprimer_numero, #ID_NUMERO, #SELF}
 */

function action_supprimer_numero_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_numero = intval($arg);

	if ($id_numero>0 AND autoriser('supprimer', 'numero', $id_numero)) {
		sql_delete('spip_numeros', "id_numero=" . sql_quote($id_numero));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_numero/$id_numero'");
	}
}

?>
