<?php

/**
 * Action de suppression d'un réseau social
 *
 * @package SPIP\Plugins\Rezosocios
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/charsets');  # pour le nom de fichier

/**
 * Effacer un réseau social
 *
 * @param null|int $id_rezosocio
 * @return void
 */
function action_supprimer_rezosocio_dist($id_rezosocio= null) {

	if (is_null($id_rezosocio)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_rezosocio = $securiser_action();
	}

	if (intval($id_rezosocio)) {

		sql_delete('spip_rezosocios', 'id_rezosocio=' . intval($id_rezosocio));

		// invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='rezosocio/$id_rezosocio'");
	}
}
