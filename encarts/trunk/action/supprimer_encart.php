<?php
/**
 * Action : supprimer un encart
 *
 * @plugin     Encarts
 * @copyright  2013-2016
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Action
 */


if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function action_supprimer_encart_dist() {
	include_spip('inc/utils');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_encart = intval($arg);

	// suppression
	include_spip('base/abstract_sql');
	sql_delete('spip_encarts', 'id_encart=' . $id_encart);
	sql_delete('spip_encarts_liens', 'id_encart=' . $id_encart);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(_request('redirect'));

}

